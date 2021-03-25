/**
 * Api functions for start, stop, get stats about analysis.
 * @since 1.1.0
 */
export function startBackgroundAnalysis(apiConfig) {
    const {baseUrl, nonce} = apiConfig;
    return fetch(baseUrl + "/background_analysis/start", {
        method: "POST",
        headers: {
            "X-WP-Nonce": nonce
        },
    })
        .then(response => response.json())
        .then(json => json);
}


export function stopBackgroundAnalysis(apiConfig) {
    const {baseUrl, nonce} = apiConfig;
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
export function restartAnalysis(apiConfig) {
    const {baseUrl, nonce} = apiConfig;
    return fetch(baseUrl + "/background_analysis/restart", {
        method: "POST",
        headers: {
            "X-WP-Nonce": nonce
        },
    })
        .then(response => response.json())
        .then(json => json);
}


export function getAnalysisStats(apiConfig) {
    const {baseUrl, nonce} = apiConfig;
    return fetch(baseUrl + "/background_analysis/stats", {
        method: "POST",
        headers: {
            "X-WP-Nonce": nonce
        },
    })
        .then(response => response.json())
        .then(json => json);
}