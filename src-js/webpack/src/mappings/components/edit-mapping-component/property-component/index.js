/**
 * PropertyComponent : used to display a individual property, has 2 states
 * allow the user to edit it and add a new property
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies
 */
import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";

/**
 * Internal dependencies
 */
import { PropertyNameField } from "./property-name-field";
import { FieldTypeField } from "./field-type-field";
import { FieldNameField } from "./field-name-field";
import { TransformFunctionField } from "./transform-function-field";
import { PropertyCloseMappingButton } from "./property-close-mapping-button";
import { WlTable } from "../../../blocks/wl-table";

class PropertyComponent extends React.Component {
  constructor(props) {
    super(props);
  }
  render() {
    return (
      <React.Fragment>
        <a className="row-title">{this.props.propData.propertyHelpText}</a>
        <br />
        <WlTable noBorder={true}>
          <tbody>
            <PropertyNameField {...this.props} />
            <FieldTypeField {...this.props} />
            <FieldNameField {...this.props} />
            <TransformFunctionField {...this.props} />
            <PropertyCloseMappingButton {...this.props} />
          </tbody>
        </WlTable>
      </React.Fragment>
    );
  }
}

// supply a property object as data
PropertyComponent.propTypes = {
  propertyData: PropTypes.object
};

const mapStateToProps = function(state) {
  return {
    transformHelpTextOptions: state.PropertyListData.transformHelpTextOptions,
    fieldTypeHelpTextOptions: state.PropertyListData.fieldTypeHelpTextOptions,
    fieldNameOptions: state.PropertyListData.fieldNameOptions
  };
};

export default connect(mapStateToProps)(PropertyComponent);
