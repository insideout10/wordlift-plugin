/**
 * Add Autocomplete select to term screen.
 *
 * @since
 */

import React from "react";
import {TermEntityAutocomplete} from "./term-entity-autocomplete";

const {__} = wp.i18n;


class SearchEntity extends React.Component {

    constructor(props) {
        super(props);
        this.props = props
        this.autocompleteTimeout = null;
        this.settings = window["_wlVocabularyTermPageSettings"];
        this.autoComplete = this.autoComplete.bind(this);
        this.onStateChange = this.onStateChange.bind(this);
    }

    onStateChange(selectedEntities) {
        this.props.addNewEntity(selectedEntities)
        return []
    }

    autoComplete(query, callback) {

        const {restUrl, nonce} = this.settings;

        //minimum 3 characters.
        if (3 > query.length) {
            callback(null, {options: []});
            return;
        }

        if (this.autocompleteTimeout !== null) {
            clearTimeout(this.autocompleteTimeout)
        }

        this.autocompleteTimeout = setTimeout(() => {
            fetch(restUrl, {
                method: "POST",
                headers: {
                    "content-type": "application/json",
                    "X-WP-Nonce": nonce
                },
                body: JSON.stringify({
                    entity: query
                })
            })
                .then(response => response.json())
                .then(json => callback(query, {options: json} ))
                .catch( (error) => {
                    console.log(error);
                })

        }, 3000);


    }

    render() {
        return (
            <React.Fragment>
                <TermEntityAutocomplete
                    loadOptions={this.autoComplete}
                    name="wl_metaboxes[entity_same_as][]"
                    placeholder=""
                    filterOption={(option, filter) => true}
                    searchPromptText={__("Type at least 3 characters to search...", "wordlift")}
                    loadingPlaceholder={__("Please wait while we look for entities...", "wordlift")}
                    noResultsText={__("No results found for your search.", "wordlift")}
                    onStateChange={this.onStateChange}
                />
                <p className="description">{__("Use the above field to search and match entities", "wordlift")}</p>
            </React.Fragment>
        )
    }
}

export default SearchEntity;
