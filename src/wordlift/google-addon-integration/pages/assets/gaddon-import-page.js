function startImport(entities) {
    console.log(entities)


}


window.addEventListener("load", () => {
    console.log("page loaded")
    // send message to parent window to retrieve the
    // window.opener.postMessage({type: "IMPORT_COMPLETE"}, "*")
    startImport(window._wlGaddonImportSettings['entity_urls'])
})