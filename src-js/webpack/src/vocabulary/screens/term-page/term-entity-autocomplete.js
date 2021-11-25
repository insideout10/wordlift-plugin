import AutocompleteSelect from "../../../Edit/components/Autocomplete/AutocompleteSelect";

export class TermEntityAutocomplete extends  AutocompleteSelect {

    onChange(value) {
        value = this.props.onStateChange(value)
        super.onChange(value);
    }

}