import {MATCH_TERMS_SETTINGS_KEY} from "../index";

export  function getTagsFromApi(offset, limit, apiConfig) {

    const {restUrl, nonce} = apiConfig;

    return fetch(restUrl, {
        method: "POST",
        headers: {
            "content-type": "application/json",
            "X-WP-Nonce": nonce
        },
        body: JSON.stringify({
            offset: offset,
            limit: limit
        })
    })
        .then(response => response.json())
        .then(json => json);
}


export function acceptEntity(termId, entityMeta,apiConfig) {

    const {baseUrl, nonce} = apiConfig;

    return fetch(baseUrl + "/entity/accept", {
        method: "POST",
        headers: {
            "content-type": "application/json",
            "X-WP-Nonce": nonce
        },
        body: JSON.stringify({
            term_id: termId,
            entity: entityMeta
        })
    })
        .then(response => response.json())
        .then(json => json);
}

export function markTagAsNoMatch(termId, apiConfig) {

    const {baseUrl, nonce} = apiConfig;

    return fetch(baseUrl + "/entity/no_match", {
        method: "POST",
        headers: {
            "content-type": "application/json",
            "X-WP-Nonce": nonce
        },
        body: JSON.stringify({
            term_id: termId,
        })
    })
        .then(response => response.json())
        .then(json => json);
}


export function undoApiCall(termId, apiConfig) {

    const {baseUrl, nonce} = apiConfig;

    return fetch(baseUrl + "/entity/undo", {
        method: "POST",
        headers: {
            "content-type": "application/json",
            "X-WP-Nonce": nonce
        },
        body: JSON.stringify({
            term_id: termId,
        })
    })
        .then(response => response.json())
        .then(json => json);
}

