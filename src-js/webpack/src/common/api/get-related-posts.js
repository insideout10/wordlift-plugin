const { ajax } = global["wp"];
const settings = global["wlSettings"];
const _wlMetaBoxSettings = global["_wlMetaBoxSettings"].settings;

export default () => {
  const entities = _wlMetaBoxSettings.entities;
  return fetch(`${ajax.settings.url}?action=wordlift_related_posts&post_id=${settings["post_id"]}`, {
    method: "POST",
    headers: { "content-type": "application/json" },
    body: JSON.stringify(Object.keys(entities))
  }).then(response => response.json());
};
