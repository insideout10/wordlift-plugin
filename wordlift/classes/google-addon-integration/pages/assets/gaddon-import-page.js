function startImport(entityUrls) {

    const state = {
        totalUrls: entityUrls.length,
        completedUrls: 0
    }

    const promises = []
    entityUrls.map((entityUrl) => {
        promises.push(getEmportEntityPromise(entityUrl, state))
    })
    Promise.all(promises).then((values) => {
        if (window.opener) {
            window.opener.postMessage({type: "IMPORT_SUCCESS"}, "*")
        }
    });

}

function updateProgressBar(state) {
    const progress = Math.round((state.completedUrls / state.totalUrls) * 100)
    document.getElementById('progress').style.width = progress + "%"

}

function getEmportEntityPromise(entityUrl, state) {

    const {restUrl, nonce} = _wlGaddonImportSettings

    return fetch(restUrl + "wordlift/v1/gaddon/import-entity", {
        method: "POST",
        headers: {"X-WP-Nonce": nonce},
        body: JSON.stringify({
            "entity_id": entityUrl
        })
    })
        .then(response => response.json())
        .catch(() => {
            state.completedUrls += 1
            updateProgressBar(state)
        })
        .then((data) => {
            state.completedUrls += 1
            updateProgressBar(state)
        });
}


window.addEventListener("load", () => {
    const {entityUrls} = _wlGaddonImportSettings

    console.log("page loaded")
    // send message to parent window to retrieve the
    startImport(entityUrls)
})