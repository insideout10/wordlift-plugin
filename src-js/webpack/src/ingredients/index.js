// Copy Table.
window.addEventListener("load", function () {
    const copyTableBtn = document.querySelector(".wl-ingredients__btn-copy-table");
    
    copyTableBtn.addEventListener("click", function () {
        const table = document.querySelector(".wp-list-table.wordlift_page_wl_ingredients");
        const range = document.createRange();
        range.selectNode(table);
        window.getSelection().addRange(range);
        document.execCommand("copy");
        window.getSelection().removeAllRanges();
        copyTableBtn.innerHTML = "Copied!";
    });
});