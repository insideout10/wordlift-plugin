// Pods Plugin Installation Notice.
window.addEventListener("load", function () {
    const pluginInstallationNotice = document.getElementById("wordlift_pods_plugin_installation_notice")
    const installPlugin = (ajaxUrl) => fetch(`${ajaxUrl}?action=wl_install_and_activate_pods`)
        .then(response => response.ok ? response.json() : Promise.reject())
    const ajaxUrl = wl_module_pods.ajax_url;
    window.wordliftInstallPods = function (installBtn) {
        installBtn.innerHTML = wl_module_pods.messages.installing;
        installPlugin(ajaxUrl)
            .catch(e => {
                pluginInstallationNotice.innerHTML = wl_module_pods.messages.installation_failed;
            })
            .then(() => {
                pluginInstallationNotice.innerHTML = wl_module_pods.messages.installation_success;
                pluginInstallationNotice.classList.remove('notice-error')
                pluginInstallationNotice.classList.add('notice-success')
            })
    };
});