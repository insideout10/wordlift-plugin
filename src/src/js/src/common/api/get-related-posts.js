const { ajax } = global["wp"];
const settings = global["wlSettings"];
const wordlift = global["wordlift"];

export default () => {
  return fetch(`${ajax.settings.url}?action=wordlift_related_posts&post_id=${settings["post_id"]}`, {
    method: "POST",
    headers: { "content-type": "application/json" },
    body: JSON.stringify(Object.keys(wordlift.entities))
  }).then(response => response.json());
};
