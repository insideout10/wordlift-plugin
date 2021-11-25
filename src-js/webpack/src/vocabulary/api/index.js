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


export function acceptEntity(termId, entityData,apiConfig) {

    const {baseUrl, nonce} = apiConfig;

    return fetch(baseUrl + "/entity/accept", {
        method: "POST",
        headers: {
            "content-type": "application/json",
            "X-WP-Nonce": nonce
        },
        body: JSON.stringify({
            term_id: termId,
            entity: entityData
        })
    })
        .then(response => response.json())
        .then(json => json);
}


export function rejectEntity(termId, entityData,apiConfig) {

    const {baseUrl, nonce} = apiConfig;

    return fetch(baseUrl + "/entity/reject", {
        method: "POST",
        headers: {
            "content-type": "application/json",
            "X-WP-Nonce": nonce
        },
        body: JSON.stringify({
            term_id: termId,
            entity: entityData
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


export function addEntityToCache(termId, apiConfig, entityData) {
    console.log("adding entity to cache")
    console.log(entityData)
    const {baseUrl, nonce} = apiConfig;

    return fetch(baseUrl + "/add-entity", {
        method: "POST",
        headers: {
            "content-type": "application/json",
            "X-WP-Nonce": nonce
        },
        body: JSON.stringify({
            term_id: termId,
            entity_data: entityData
        })
    })
        .then(response => response.json())
        .then(json => json);
}

