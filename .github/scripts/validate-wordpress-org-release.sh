#!/usr/bin/env bash

set -Eeuo pipefail

if [[ $# -ne 2 ]]; then
	echo "Usage: $0 <repository> <version>" >&2
	exit 2
fi

repository=$1
version=$2

if [[ ! $version =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
	echo "Invalid release version: $version" >&2
	exit 1
fi

if [[ ! -d $repository/.git ]]; then
	echo "Not a Git repository: $repository" >&2
	exit 1
fi

tag_commit=$(git -C "$repository" rev-parse --verify "refs/tags/$version^{commit}")
head_commit=$(git -C "$repository" rev-parse HEAD)

if [[ $tag_commit != "$head_commit" ]]; then
	echo "Tag $version does not point at the checked-out commit." >&2
	exit 1
fi

if ! git -C "$repository" merge-base --is-ancestor "$head_commit" origin/main; then
	echo "Tag $version does not point to a commit on origin/main." >&2
	exit 1
fi

header_version=$(sed -nE 's/^ \* Version:[[:space:]]+([0-9]+\.[0-9]+\.[0-9]+)$/\1/p' "$repository/src/wordlift.php")
constant_version=$(sed -nE "s/^define\( 'WORDLIFT_VERSION', '([0-9]+\.[0-9]+\.[0-9]+)' \);$/\1/p" "$repository/src/wordlift.php")
stable_version=$(sed -nE 's/^Stable tag:[[:space:]]+([0-9]+\.[0-9]+\.[0-9]+)$/\1/p' "$repository/src/readme.txt")
changelog_version=$(sed -nE 's/^= ([0-9]+\.[0-9]+\.[0-9]+)( \([^)]*\))? =$/\1/p' "$repository/src/changelog.txt" | head -n 1)

for detected_version in "$header_version" "$constant_version" "$stable_version" "$changelog_version"; do
	if [[ $detected_version != "$version" ]]; then
		echo "Release version mismatch: expected $version, found ${detected_version:-missing}." >&2
		exit 1
	fi
done

echo "Validated WordLift release $version at $head_commit."
