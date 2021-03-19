/**
 * Api functions for start, stop, get stats about analysis.
 * @since 1.1.0
 */
import {MATCH_TERMS_SETTINGS_KEY} from "../../index";

export function startBackgroundAnalysis() {
    const {baseUrl, nonce} = global[MATCH_TERMS_SETTINGS_KEY];
    return fetch(baseUrl + "/background_analysis/start", {
        method: "POST",
        headers: {
            "X-WP-Nonce": nonce
        },
    })
        .then(response => response.json())
        .then(json => json);
}


export function stopBackgroundAnalysis() {
    const {baseUrl, nonce} = global[MATCH_TERMS_SETTINGS_KEY];
    return fetch(baseUrl + "/background_analysis/stop", {
        method: "POST",
        headers: {
            "X-WP-Nonce": nonce
        },
    })
        .then(response => response.json())
        .then(json => json);
}

/**
 * Removes previously cached analysis results.
 * @returns {*}
 */
export function restartAnalysis() {
    const {baseUrl, nonce} = global[MATCH_TERMS_SETTINGS_KEY];
    return fetch(baseUrl + "/background_analysis/restart", {
        method: "POST",
        headers: {
            "X-WP-Nonce": nonce
        },
    })
        .then(response => response.json())
        .then(json => json);
}


export function getAnalysisStats() {
    const {baseUrl, nonce} = global[MATCH_TERMS_SETTINGS_KEY];
    return fetch(baseUrl + "/background_analysis/stats", {
        method: "POST",
        headers: {
            "X-WP-Nonce": nonce
        },
    })
        .then(response => response.json())
        .then(json => json);
}