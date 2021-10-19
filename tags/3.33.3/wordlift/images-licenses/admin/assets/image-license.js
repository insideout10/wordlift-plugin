(function(wp, settings) {
  window.addEventListener("load", function() {
    document.querySelectorAll(".wl-action-btn").forEach(el => {
      el.addEventListener("click", function() {
        const id = el.getAttribute("data-id");
        const rowId = el.getAttribute("data-row-id");
        const action = el.getAttribute("data-action");
        const scriptEl = document.getElementById(id);
        if (null === scriptEl) {
          alert(settings["l10n"]["An error occurred"] + " (1)");
          return;
        }

        const data = Object.assign({}, JSON.parse(scriptEl.innerText), {
          _ajax_nonce: settings["_ajax_nonce"][action]
        });

        wp.ajax
          .post(action, data)
          .done(_ => document.getElementById(rowId).classList.add("hidden"))
          .fail(_ => alert(settings["l10n"]["An error occurred"] + " (2)"));
      });
    });
  });
})(window["wp"], window["_wlImageLicensePageSettings"]);
