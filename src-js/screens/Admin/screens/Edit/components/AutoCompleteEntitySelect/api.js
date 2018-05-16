export function autocomplete(query, language, ...excludes) {
  const url = new URL("http://localhost:8080/wordlift-api/autocomplete");

  url.searchParams.append("query", query);
  url.searchParams.append("language", language);

  if (0 === excludes.length) url.searchParams.append("exclude", "");
  else excludes.forEach(value => url.searchParams.append("exclude", value));

  return fetch(url).then(response => response.json());
}
