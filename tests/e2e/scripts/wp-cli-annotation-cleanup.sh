#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../../.." && pwd)"
PROJECT_NAME="${WORDLIFT_E2E_PROJECT_NAME:-wordlift-wp-cli-e2e}"
COMPOSE_FILE="${ROOT_DIR}/tests/e2e/docker-compose.wp-cli.yml"

compose=(docker compose -f "${COMPOSE_FILE}" -p "${PROJECT_NAME}")
wp=( "${compose[@]}" exec -T wordpress wp --allow-root )

cleanup() {
  if [ "${WORDLIFT_E2E_KEEP:-0}" != "1" ]; then
    "${compose[@]}" down -v --remove-orphans >/dev/null 2>&1 || true
  fi
}

fail() {
  echo "ERROR: $*" >&2
  exit 1
}

assert_contains_line() {
  local haystack="$1"
  local needle="$2"

  if ! printf '%s\n' "${haystack}" | grep -qx "${needle}"; then
    printf '%s\n' "${haystack}" >&2
    fail "Expected output to contain a line equal to '${needle}'."
  fi
}

assert_content() {
  local post_id="$1"
  local expected="$2"
  local actual

  actual="$("${wp[@]}" post get "${post_id}" --field=post_content)"

  if [ "${actual}" != "${expected}" ]; then
    printf 'Expected post %s content:\n%s\n\nActual:\n%s\n' "${post_id}" "${expected}" "${actual}" >&2
    fail "Unexpected post content."
  fi
}

set_raw_post_content() {
  local post_id="$1"
  local content="$2"
  local encoded

  encoded="$(printf '%s' "${content}" | base64 | tr -d '\n')"

  "${wp[@]}" eval "
    global \$wpdb;

    \$post_id = absint( ${post_id} );
    \$content = base64_decode( '${encoded}' );

    \$wpdb->update( \$wpdb->posts, array( 'post_content' => \$content ), array( 'ID' => \$post_id ) );
    clean_post_cache( \$post_id );
  "
}

wait_for_wordpress() {
  local attempt

  for attempt in $(seq 1 60); do
    if "${wp[@]}" core is-installed >/dev/null 2>&1; then
      return 0
    fi

    sleep 5
  done

  "${compose[@]}" logs wordpress >&2 || true
  fail "WordPress did not become ready."
}

cleanup
"${compose[@]}" up -d db nginx wordpress
trap cleanup EXIT

wait_for_wordpress

if ! "${wp[@]}" plugin is-active wordlift >/dev/null 2>&1; then
  "${wp[@]}" plugin activate wordlift
fi

broken_content='The plug<span id="urn:e2e-broken" class="textannotation">in</span> is active.'
other_broken_content='The Kinsta AP<span id="urn:e2e-other-broken" class="textannotation disambiguated wl-thing">I alre</span>ady has endpoints.'
valid_content='The <span id="urn:e2e-valid" class="textannotation">plugin</span> is active.'

target_post_id="$("${wp[@]}" post create --post_type=post --post_status=publish --post_title='WP-CLI cleanup e2e target' --porcelain)"
other_post_id="$("${wp[@]}" post create --post_type=post --post_status=publish --post_title='WP-CLI cleanup e2e other' --porcelain)"
valid_post_id="$("${wp[@]}" post create --post_type=post --post_status=publish --post_title='WP-CLI cleanup e2e valid' --porcelain)"

set_raw_post_content "${target_post_id}" "${broken_content}"
set_raw_post_content "${other_post_id}" "${other_broken_content}"
set_raw_post_content "${valid_post_id}" "${valid_content}"

dry_run_output="$("${wp[@]}" wordlift annotations cleanup --post_ids="${target_post_id},${valid_post_id}" --debug 2>&1)"
assert_contains_line "${dry_run_output}" "${target_post_id}"
assert_content "${target_post_id}" "${broken_content}"
assert_content "${valid_post_id}" "${valid_content}"

apply_output="$("${wp[@]}" wordlift annotations cleanup --post_ids="${target_post_id},${valid_post_id}" --apply --debug 2>&1)"
assert_contains_line "${apply_output}" "${target_post_id}"
assert_content "${target_post_id}" 'The plugin is active.'
assert_content "${valid_post_id}" "${valid_content}"
assert_content "${other_post_id}" "${other_broken_content}"

post_apply_output="$("${wp[@]}" wordlift annotations cleanup --post_ids="${target_post_id}" --debug 2>&1)"

if printf '%s\n' "${post_apply_output}" | grep -qx "${target_post_id}"; then
  printf '%s\n' "${post_apply_output}" >&2
  fail "Dry-run reported cleaned post ${target_post_id} as still affected."
fi

echo "WP-CLI annotation cleanup e2e passed."
