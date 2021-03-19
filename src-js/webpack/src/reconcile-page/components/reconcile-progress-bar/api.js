export function getReconcileProgress() {
    const {baseUrl, nonce} = global["_wlCmKgConfig"];
    return fetch(baseUrl + "/reconcile_progress/progress", {
        method: "POST",
        headers: {
            "X-WP-Nonce": nonce
        },
    })
        .then(response => response.json())
        .then(json => json);
}