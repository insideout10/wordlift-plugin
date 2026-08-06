#!/usr/bin/env bash

set -Eeuo pipefail

if [[ $# -ne 5 ]]; then
	echo "Usage: $0 <distribution> <svn-url> <version> <maximum-changes> <allow-large-diff>" >&2
	exit 2
fi

distribution=$1
svn_url=${2%/}
version=$3
maximum_changes=$4
allow_large_diff=$5

if [[ ! $version =~ ^[0-9]+\.[0-9]+\.[0-9]+$ || ! $maximum_changes =~ ^[0-9]+$ ]]; then
	echo "Invalid version or maximum change count." >&2
	exit 1
fi

if [[ $allow_large_diff != true && $allow_large_diff != false ]]; then
	echo "allow-large-diff must be true or false." >&2
	exit 1
fi

if [[ ! -d $distribution/plugin ]]; then
	echo "The prepared plugin directory is missing." >&2
	exit 1
fi

svn_directory=$(mktemp -d "${TMPDIR:-/tmp}/wordlift-svn.XXXXXX")
manifest=$(mktemp "${TMPDIR:-/tmp}/wordlift-manifest.XXXXXX")
trap 'rm -rf "$svn_directory" "$manifest"' EXIT

svn checkout --quiet --depth immediates "$svn_url" "$svn_directory"
svn update --quiet --set-depth infinity "$svn_directory/trunk"
svn update --quiet --set-depth immediates "$svn_directory/tags"

write_manifest() {
	local source_directory=$1
	local target_directory=$2
	local scope=$3

	while IFS= read -r line; do
		if [[ $line == '*deleting '* ]]; then
			path=${line#'*deleting '}
			path=${path#"${path%%[![:space:]]*}"}
			if [[ $path != */ ]]; then
				printf 'D\t%s\t%s\n' "$scope" "$path"
			fi
			continue
		fi

		item=${line%%|*}
		path=${line#*|}
		if [[ $item =~ ^\>f\++$ ]]; then
			printf 'A\t%s\t%s\n' "$scope" "$path"
		elif [[ $item == '>f'* ]]; then
			printf 'M\t%s\t%s\n' "$scope" "$path"
		fi
	done < <(
		rsync -rcn --delete --exclude='.svn/' --itemize-changes \
			--out-format='%i|%n' "$source_directory/" "$target_directory/"
	)
}

emit_output() {
	local name=$1
	local value=$2

	if [[ -n ${GITHUB_OUTPUT:-} ]]; then
		printf '%s=%s\n' "$name" "$value" >> "$GITHUB_OUTPUT"
	fi
}

if [[ -d $svn_directory/tags/$version ]]; then
	svn update --quiet --set-depth infinity "$svn_directory/tags/$version"
	write_manifest "$distribution/plugin" "$svn_directory/tags/$version" plugin > "$manifest"
	if [[ -s $manifest ]]; then
		echo "WordPress.org tag $version already exists with different contents." >&2
		cat "$manifest" >&2
		exit 1
	fi

	emit_output already_exists true
	emit_output changed_files 0
	echo "WordPress.org tag $version already matches the release; deployment is a no-op."
	exit 0
fi

write_manifest "$distribution/plugin" "$svn_directory/trunk" plugin | LC_ALL=C sort > "$manifest"

changed_files=$(wc -l < "$manifest" | tr -d ' ')
emit_output already_exists false
emit_output changed_files "$changed_files"

echo "WordPress.org deployment manifest ($changed_files changed files):"
cat "$manifest"

if [[ -n ${GITHUB_STEP_SUMMARY:-} ]]; then
	{
		echo '### WordPress.org deployment manifest'
		echo
		echo "Changed files: **$changed_files**"
		echo
		echo '```text'
		cat "$manifest"
		echo '```'
	} >> "$GITHUB_STEP_SUMMARY"
fi

if (( changed_files > maximum_changes )) && [[ $allow_large_diff != true ]]; then
	echo "Refusing to deploy $changed_files files; the limit is $maximum_changes." >&2
	exit 1
fi
