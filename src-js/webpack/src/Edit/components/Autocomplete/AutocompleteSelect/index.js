/**
 * Components: Autocomplete Select.
 *
 * The Autocomplete Select displays Autocomplete results returned by the WordLift Autocomplete End-point.
 *
 * @since 1.0.0
 */

/**
 * Stylesheets.
 */
import "./index.css";

/**
 * External dependencies.
 */
import React, { Component } from "react";
import PropTypes from "prop-types";
import Select from "react-select";

/**
 * Internal dependencies.
 */
import AutocompleteResultOption from "../AutocompleteResultOption";
import AutocompleteResultValue from "../AutocompleteResultValue";

const isOptionUnique = ({ option, options, labelKey, valueKey }) =>
  options && -1 === options.findIndex(x => option[valueKey] === x[valueKey]);

const isValidNewOption = ({ label }) => label && label.match(/^https?:\/\/.+/i);

const newOptionCreator = ({ label, labelKey, valueKey }) => {
  return {
    [labelKey]: label,
    [valueKey]: label
  };
};

/**
 * AutocompleteSelect stateless component.
 *
 * @since 1.0.0
 */
class AutocompleteSelect extends Component {
  /**
   * Create an {@link AutocompleteSelect} instance.
   *
   *
   * @param {boolean} autoload Whether to autoload data.
   * @param {Function} loadOptions The function used to load the list of options. The
   *                          function must accept two arguments: the input string
   *                          and the callback to send the arguments.
   * @param {Function} optionRenderer A React stateless component for rendering
   *                          the option.
   * @param {Function} valueRenderer A React stateless component for rendering the
   *                          selected value.
   * @param props
   *
   *
   * @since 1.0.0
   * @param {Object} props Rendering properties.
   */
  constructor(props) {
    super(props);

    // Bind the `onChange` function.
    this.onChange = this.onChange.bind(this);

    // Set the initial state.
    this.state = { value: "" };
  }

  /**
   * Catch `onChange` events and update the state.
   *
   * @since 1.0.0
   *
   * @param {Object|Array} value The value.
   */
  onChange(value) {
    this.setState({ value });
  }

  /**
   * Render the component.
   *
   * @since 1.0.0
   * @returns {XML}
   */
  render() {
    const {
      autoload,
      loadOptions,
      optionRenderer,
      valueComponent,
      ...props
    } = this.props;

    const multi = this.props.multi !== undefined ? this.props.multi : true;

    return (
      <Select.AsyncCreatable
        autoload={autoload}
        ignoreAccents={true}
        ignoreCase={true}
        loadOptions={loadOptions}
        optionRenderer={optionRenderer}
        valueComponent={valueComponent}
        openOnFocus={true}
        autoBlur={true}
        multi={multi}
        onChange={this.onChange}
        value={this.state.value}
        newOptionCreator={newOptionCreator}
        isValidNewOption={isValidNewOption}
        isOptionUnique={isOptionUnique}
        // matchProp="value"
        clearable={false}
        {...props}
      />
    );
  }
}

/**
 * Define the property types.
 *
 * @since 1.0.0
 *
 * @type {{autoload: *, loadOptions, optionRenderer: *, valueComponent: *}}
 */
AutocompleteSelect.propTypes = {
  autoload: PropTypes.bool,
  loadOptions: PropTypes.func.isRequired,
  optionRenderer: PropTypes.func,
  valueComponent: PropTypes.func
};

/**
 * Define the default properties.
 *
 * @since 1.0.0
 *
 * @type {{autoload: boolean, optionRenderer: (function({images: array, labels: array, scope: string, displayTypes: string, descriptions: *}): XML), valueRenderer: (function({images: array, labels: array, scope: string, displayTypes: string, descriptions: *}): XML), filterOptions: (function(*, *, *))}}
 */
AutocompleteSelect.defaultProps = {
  autoload: false,
  optionRenderer: AutocompleteResultOption,
  valueComponent: AutocompleteResultValue
};

// Finally export the AutocompleteSelect.
export default AutocompleteSelect;
