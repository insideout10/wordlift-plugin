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

        this.autoComplete = this.autoComplete.bind(this);
    }

    autoComplete() {

    }

    render() {
        return (
            <React.Fragment>
                <AutocompleteSelect
                    loadOptions={this.autoComplete}
                />
                <p className="description">{__("Use the above field to search and match entities", "wordlift")}</p>
            </React.Fragment>
        )
    }
}

export default SearchEntity;
