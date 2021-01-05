/**
 * This file contains the functions created to handle the admin side plaats
 * enhance functionality.
 */

(function($) {
  "use strict";

  const progressBarFn = function() {
    let index = 0;

    const onClickHandlerFn = function(progressBarEl) {
      return function(event) {
        const el = event.target;
        const action = el.getAttribute("data-action");
        const nonce = el.getAttribute("data-nonce");
        el.classList.add("hidden");

        return request(action, nonce, index, function(data) {
          progress(progressBarEl, (index = data.index), data.count);
          return data;
        });
      };
    };

    return function(taskEl) {
      const progressBarEl = taskEl.querySelector(".wl-task__progress__bar");
      taskEl
        .querySelector("button")
        .addEventListener("click", onClickHandlerFn(progressBarEl));
    };
  };

  document.querySelectorAll(".wl-task").forEach(progressBarFn());

  function progress(el, index, count) {
    const current = (100 * (index + 1)) / count;
    el.style.width = current + "%";
    el.innerText = Math.round(current) + "%";
  }

  function request(action, nonce, offset, callback) {
    return wp.ajax
      .post(action, {
        _ajax_nonce: nonce,
        offset: offset
      })
      .then(callback)
      .then(function(data) {
        if (!data.complete)
          return request(action, data.nonce, data.index + 1, callback);
      });
  }
})(jQuery);
