#!/usr/bin/env bash

set -Eeuo pipefail

if [[ $# -ne 2 ]]; then
	echo "Usage: $0 <repository> <output-directory>" >&2
	exit 2
fi

repository=$1
output_directory=$2

if [[ ! -d $repository/.git || ! -f $repository/src/.distignore ]]; then
	echo "The repository or src/.distignore is missing." >&2
	exit 1
fi

if [[ -e $output_directory ]]; then
	echo "Output directory already exists: $output_directory" >&2
	exit 1
fi

if git -C "$repository" ls-files -s src assets | awk '$1 == "120000" { found = 1 } END { exit !found }'; then
	echo "Symlinks are not allowed in the WordPress.org distribution." >&2
	exit 1
fi

while IFS= read -r -d '' path; do
	if [[ $path == *$'\n'* || $path == *$'\r'* || $path == *$'\t'* ]]; then
		echo "Control characters are not allowed in distribution paths." >&2
		exit 1
	fi
done < <(git -C "$repository" ls-files -z src assets)

export_directory=$(mktemp -d "${TMPDIR:-/tmp}/wordlift-export.XXXXXX")
trap 'rm -rf "$export_directory"' EXIT

mkdir -p "$output_directory/plugin"
git -C "$repository" -c core.autocrlf=true checkout-index --all --prefix="$export_directory/"

rsync -rc --exclude-from="$repository/src/.distignore" \
	"$export_directory/src/" "$output_directory/plugin/"

echo "Prepared WordPress.org distribution in $output_directory."
