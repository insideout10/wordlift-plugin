export function getVideosFromApi(apiConfig) {
    const {restUrl, nonce, postId} = apiConfig;

    return fetch(restUrl, {
        method: "POST",
        headers: {
            "content-type": "application/json",
            "X-WP-Nonce": nonce
        },
        body: JSON.stringify({
            post_id: postId,
        })
    })
        .then(response => response.json())
        .then(json => json);
}


export function saveVideos(apiConfig,videos) {

    const {restUrl, nonce, postId} = apiConfig;

    return fetch(restUrl + "/videos/save", {
        method: "POST",
        headers: {
            "content-type": "application/json",
            "X-WP-Nonce": nonce
        },
        body: JSON.stringify({
            post_id: postId,
            videos: videos
        })
    })
        .then(response => response.json())
        .then(json => json);
}
