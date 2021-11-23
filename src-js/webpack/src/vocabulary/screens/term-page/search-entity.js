/**
 * Add Autocomplete select to term screen.
 *
 * @since
 */

import React from "react";
import AutocompleteSelect from "../../../Edit/components/Autocomplete/AutocompleteSelect";

const {__} = wp.i18n;


class SearchEntity extends React.Component {

    constructor(props) {
        super(props);
        this.settings = window["_wlVocabularyTermPageSettings"];
        this.autoComplete = this.autoComplete.bind(this);
    }

    autoComplete(query, callback) {

        const {restUrl, nonce} = this.settings;
        let autocompleteTimeout = null;

        //minimum 3 characters.
        if (3 > query.length) {
            callback(null, {options: []});
            return;
        }

        if (autocompleteTimeout !== null) {
            clearTimeout(autocompleteTimeout)
        }

        autocompleteTimeout = setTimeout(() => {
            fetch(restUrl + query, {
                method: "POST",
                headers: {
                    "content-type": "application/json",
                    "X-WP-Nonce": nonce
                },
                body: JSON.stringify({
                    query: query
                })
            })
                .then(response => response.json())
                .then(json => callback(query, {options: json} ))
                .catch( (error) => {
                    console.log(error);
                })

        }, 1000);


    }

    render() {
        return (
            <React.Fragment>
                <AutocompleteSelect
                    loadOptions={this.autoComplete}
                    name="wl_metaboxes[entity_same_as][]"
                    placeholder=""
                    filterOption={(option, filter) => true}
                    searchPromptText={__("Type at least 3 characters to search...", "wordlift")}
                    loadingPlaceholder={__("Please wait while we look for entities...", "wordlift")}
                    noResultsText={__("No results found for your search.", "wordlift")}
                />
                <p className="description">{__("Use the above field to search and match entities", "wordlift")}</p>
            </React.Fragment>
        )
    }
}

export default SearchEntity;
