/**
 * Internal dependencies.
 */
import "./index.scss"

window.addEventListener("load", () => {
    const element = document.getElementById("wl_entity_type_search");

    // Check that the document element is there.
    if (null === element) {
        return;
    }

    element.addEventListener("keyup", e => {
        let filter, ul, li, i, txtValue;
        filter = element.value.toUpperCase();
        ul = document.getElementById("wl-entity-type__ul");
        li = ul.getElementsByTagName("li");

        // Loop through all list items, and hide those who don't match the search query
        for (i = 0; i < li.length; i++) {
            txtValue = li[i].getElementsByTagName("label")[0].innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = "";
            } else {
                li[i].style.display = "none";
            }
        }
    });
});