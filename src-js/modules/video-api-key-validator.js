/**
 * Validators: Video API Key Validator.
 *
 * Validate WordLift"s Video Settings API key in inputs.
 *
 * @since 3.40.2
 */

/**
 * Create a Video API key validator on the element with the specified selector.
 *
 * @since 3.40.2
 * @param {string} selector The element selector.
 * @param {string} type Type.
 */
export const VideoAPIKeyValidator = (selector, type) => {
  ["keyup", "paste"].forEach( event => {
    selector.addEventListener( event, () => {
      selector.classList.remove( "untouched", "valid", "invalid");
      delay( selector, () => {
        ApiKeyValidator(selector, type);
      })
    });
  });

};

export const ApiKeyValidator = (selector, type) => {
  const settings = window["wlSettings"] || {};
  const apiKey = selector.value;
  selector.classList.add("loading");

  // Post the validation request.
  window['wp'].ajax
  .post(
    "wl_validate_video_api_key", {
    api_key: apiKey,
    type: type,
    _wpnonce: settings["wl_video_api_nonce"],
  })
  .done( () => {
    selector.classList.replace("loading","valid");
  })
  .fail( () => {
    selector.classList.replace("loading","invalid");
  });
}

const delay = (elem, fn, timeout = 500, ...args) => {
  clearTimeout(elem.getAttribute("data-timeout"));

  elem.setAttribute(
    "data-timeout",
    setTimeout(fn, timeout, ...args)
  );
};
