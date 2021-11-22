/**
 * Add Autocomplete select to term screen.
 *
 * @since
 */

import React from "react";
import AutocompleteSelect from "../../../Edit/components/Autocomplete/AutocompleteSelect";


class SearchEntity extends React.Component {

    constructor(props) {
        super(props);

        this.autoComplete = this.autoComplete.bind(this);
    }

    autoComplete() {

    }

    render() {
        return(
            <React.Fragment>
                <AutocompleteSelect
                    loadOptions={this.autoComplete}
                />,
            </React.Fragment>
        )
    }
}

export default SearchEntity;
