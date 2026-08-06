#!/usr/bin/env bash

set -Eeuo pipefail

script_directory=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
test_directory=$(mktemp -d "${TMPDIR:-/tmp}/wordlift-deploy-test.XXXXXX")
trap 'rm -rf "$test_directory"' EXIT

fail() {
	echo "FAIL: $*" >&2
	exit 1
}

assert_contains() {
	local expected=$1
	local file=$2
	grep -Fqx "$expected" "$file" || fail "Missing '$expected' in $file"
}

svn_repository=$test_directory/svn
svn_url="file://$svn_repository"
svnadmin create "$svn_repository"
svn mkdir --quiet "$svn_url/trunk" "$svn_url/tags" "$svn_url/assets" -m 'Initialize plugin repository'

svn_working_copy=$test_directory/svn-working-copy
svn checkout --quiet "$svn_url" "$svn_working_copy"
printf 'unchanged\r\n' > "$svn_working_copy/trunk/unchanged.txt"
printf 'old value\r\n' > "$svn_working_copy/trunk/modified.txt"
printf 'delete me\r\n' > "$svn_working_copy/trunk/deleted.txt"
printf '\000\001unchanged' > "$svn_working_copy/trunk/unchanged.bin"
printf '\000\001old' > "$svn_working_copy/trunk/modified.bin"
printf 'old asset' > "$svn_working_copy/assets/icon.png"
svn add --quiet "$svn_working_copy/trunk"/* "$svn_working_copy/assets"/*
svn commit --quiet "$svn_working_copy" -m 'Seed previous release'

release_repository=$test_directory/release
mkdir -p "$release_repository/src/nested" "$release_repository/assets"
git init --quiet --initial-branch=main "$release_repository"
git -C "$release_repository" config user.email test@example.com
git -C "$release_repository" config user.name 'WordLift test'

cat > "$release_repository/src/wordlift.php" <<'PHP'
<?php
/**
 * Version:           1.2.3
 */
define( 'WORDLIFT_VERSION', '1.2.3' );
PHP
printf 'Stable tag: 1.2.3\n' > "$release_repository/src/readme.txt"
printf '= 1.2.3 (2026-08-06) =\n* Test release.\n' > "$release_repository/src/changelog.txt"
printf 'unchanged\n' > "$release_repository/src/unchanged.txt"
printf 'new value\n' > "$release_repository/src/modified.txt"
printf 'new nested file\n' > "$release_repository/src/nested/file with spaces.txt"
printf '\000\001unchanged' > "$release_repository/src/unchanged.bin"
printf '\000\001new' > "$release_repository/src/modified.bin"
printf 'ignore me\n' > "$release_repository/src/.nvmrc"
printf 'ignore me\n' > "$release_repository/src/scoper.inc.php"
cat > "$release_repository/src/.distignore" <<'EOF'
.git*
.nvmrc
.distignore
scoper.inc.php
EOF
printf 'new asset' > "$release_repository/assets/icon.png"

git -C "$release_repository" add .
git -C "$release_repository" commit --quiet -m 'Release 1.2.3'
git -C "$release_repository" tag 1.2.3
git clone --quiet --bare "$release_repository" "$test_directory/origin.git"
git -C "$release_repository" remote add origin "$test_directory/origin.git"
git -C "$release_repository" fetch --quiet origin main

"$script_directory/validate-wordpress-org-release.sh" "$release_repository" 1.2.3
if "$script_directory/validate-wordpress-org-release.sh" "$release_repository" 1.2.4 2> /dev/null; then
	fail 'A mismatched release version was accepted.'
fi

distribution=$test_directory/distribution
"$script_directory/prepare-wordpress-org-dist.sh" "$release_repository" "$distribution"

[[ ! -e $distribution/plugin/.distignore ]] || fail '.distignore was packaged.'
[[ ! -e $distribution/plugin/.nvmrc ]] || fail '.nvmrc was packaged.'
[[ ! -e $distribution/plugin/scoper.inc.php ]] || fail 'scoper.inc.php was packaged.'
[[ -f "$distribution/plugin/nested/file with spaces.txt" ]] || fail 'A path containing spaces was lost.'
file "$distribution/plugin/unchanged.txt" | grep -q CRLF || fail 'Text files were not exported with CRLF endings.'
cmp "$release_repository/src/modified.bin" "$distribution/plugin/modified.bin" || fail 'Binary data changed during export.'

outputs=$test_directory/outputs
summary=$test_directory/summary
revision_before=$(svnlook youngest "$svn_repository")
GITHUB_OUTPUT=$outputs GITHUB_STEP_SUMMARY=$summary \
	"$script_directory/preflight-wordpress-org-deploy.sh" "$distribution" "$svn_url" 1.2.3 100 false
revision_after=$(svnlook youngest "$svn_repository")

[[ $revision_before == "$revision_after" ]] || fail 'Preflight changed the SVN repository.'
assert_contains 'already_exists=false' "$outputs"
assert_contains 'changed_files=7' "$outputs"
assert_contains $'A\tplugin\tchangelog.txt' "$summary"
assert_contains $'A\tplugin\tnested/file with spaces.txt' "$summary"
assert_contains $'D\tplugin\tdeleted.txt' "$summary"
assert_contains $'M\tplugin\tmodified.bin' "$summary"
assert_contains $'M\tplugin\tmodified.txt' "$summary"
if grep -Fq $'\tassets\t' "$summary"; then
	fail 'WordPress.org assets must not be changed by a plugin release.'
fi
if grep -Fq 'unchanged.txt' "$summary"; then
	fail 'An LF/CRLF-only difference was reported as a change.'
fi
if grep -Fq 'unchanged.bin' "$summary"; then
	fail 'An unchanged binary was reported as a change.'
fi

if "$script_directory/preflight-wordpress-org-deploy.sh" "$distribution" "$svn_url" 1.2.3 1 false; then
	fail 'The large-diff guard did not stop deployment.'
fi
"$script_directory/preflight-wordpress-org-deploy.sh" "$distribution" "$svn_url" 1.2.3 1 true > /dev/null

rsync -rc --delete --exclude='.svn/' "$distribution/plugin/" "$svn_working_copy/trunk/"
svn add --quiet --force "$svn_working_copy/trunk"
svn status "$svn_working_copy" | awk '$1 == "!" { sub(/^! +/, ""); print }' | while IFS= read -r path; do
	svn rm --quiet "$path"
done
svn copy --quiet "$svn_working_copy/trunk" "$svn_working_copy/tags/1.2.3"
svn commit --quiet "$svn_working_copy" -m 'Deploy 1.2.3'

existing_outputs=$test_directory/existing-outputs
GITHUB_OUTPUT=$existing_outputs \
	"$script_directory/preflight-wordpress-org-deploy.sh" "$distribution" "$svn_url" 1.2.3 100 false
assert_contains 'already_exists=true' "$existing_outputs"
assert_contains 'changed_files=0' "$existing_outputs"

tag_working_copy=$test_directory/tag-working-copy
svn checkout --quiet "$svn_url/tags/1.2.3" "$tag_working_copy"
printf 'diverged\n' > "$tag_working_copy/modified.txt"
svn commit --quiet "$tag_working_copy" -m 'Introduce tag divergence'
if "$script_directory/preflight-wordpress-org-deploy.sh" "$distribution" "$svn_url" 1.2.3 100 false; then
	fail 'A divergent immutable tag was accepted.'
fi

echo 'All WordPress.org deployment integration tests passed.'
