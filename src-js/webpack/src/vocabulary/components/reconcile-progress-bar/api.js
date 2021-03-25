import {MATCH_TERMS_SETTINGS_KEY} from "../../index";

export function getReconcileProgress() {
    const {baseUrl, nonce} = global[MATCH_TERMS_SETTINGS_KEY];
    return fetch(baseUrl + "/reconcile_progress/progress", {
        method: "POST",
        headers: {
            "X-WP-Nonce": nonce
        },
    })
        .then(response => response.json())
        .then(json => json);
}