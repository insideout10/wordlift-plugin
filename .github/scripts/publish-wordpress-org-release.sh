#!/usr/bin/env bash

set -Eeuo pipefail

if [[ $# -ne 4 ]]; then
	echo "Usage: $0 <distribution> <svn-url> <version> <dry-run>" >&2
	exit 2
fi

distribution=$1
svn_url=${2%/}
version=$3
dry_run=$4

if [[ ! $version =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
	echo "Invalid release version: $version" >&2
	exit 1
fi

if [[ $dry_run != true && $dry_run != false ]]; then
	echo "dry-run must be true or false." >&2
	exit 1
fi

if [[ ! -d $distribution/plugin || ! -d $distribution/assets-overlay ]]; then
	echo "The prepared plugin or asset overlay directory is missing." >&2
	exit 1
fi

if [[ $dry_run == false && ( -z ${SVN_USERNAME:-} || -z ${SVN_PASSWORD:-} ) ]]; then
	echo "SVN_USERNAME and SVN_PASSWORD are required for publication." >&2
	exit 1
fi

svn_directory=$(mktemp -d "${TMPDIR:-/tmp}/wordlift-publish.XXXXXX")
trap 'rm -rf "$svn_directory"' EXIT

svn checkout --quiet --depth immediates "$svn_url" "$svn_directory"
svn update --quiet --set-depth infinity "$svn_directory/assets"
svn update --quiet --set-depth infinity "$svn_directory/trunk"
svn update --quiet --set-depth immediates "$svn_directory/tags"

if [[ -d $svn_directory/tags/$version ]]; then
	echo "WordPress.org tag $version already exists; publication is a no-op."
	exit 0
fi

rsync -rc --delete --exclude='.svn/' "$distribution/plugin/" "$svn_directory/trunk/"
rsync -rc "$distribution/assets-overlay/" "$svn_directory/assets/"

svn add --quiet --force "$svn_directory/trunk" "$svn_directory/assets"

while IFS= read -r status_line; do
	if [[ ${status_line:0:1} == '!' ]]; then
		svn rm --quiet "${status_line:8}"
	fi
done < <(svn status "$svn_directory/trunk")

while IFS= read -r status_line; do
	if [[ ${status_line:0:1} != 'A' && ${status_line:0:1} != 'M' ]]; then
		continue
	fi

	path=${status_line:8}
	case $path in
		*.[Pp][Nn][Gg]) mime_type=image/png ;;
		*.[Jj][Pp][Gg]|*.[Jj][Pp][Ee][Gg]) mime_type=image/jpeg ;;
		*.[Gg][Ii][Ff]) mime_type=image/gif ;;
		*.[Ss][Vv][Gg]) mime_type=image/svg+xml ;;
		*) continue ;;
	esac
	svn propset --quiet svn:mime-type "$mime_type" "$path"
done < <(svn status "$svn_directory/assets")

svn copy --quiet "$svn_directory/trunk" "$svn_directory/tags/$version"
svn update --quiet "$svn_directory"
svn status "$svn_directory"

if [[ $dry_run == true ]]; then
	echo "Dry run complete; no WordPress.org changes were committed."
	exit 0
fi

svn commit "$svn_directory" \
	--message "Update to version $version from GitHub" \
	--no-auth-cache \
	--non-interactive \
	--username "$SVN_USERNAME" \
	--password "$SVN_PASSWORD"

echo "Published WordLift $version to WordPress.org."
