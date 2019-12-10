/**
 * @since 3.24.0
 * 
 * PropertyComponent : used to display a individaul property, has 2 states
 * allow the user to edit it and add a new property
 */

import React from 'react'
import PropTypes from 'prop-types';

 class PropertyComponent extends React.Component {
     constructor (props) {
         super(props)
     }
     state = {
         propertyHelpText: ( this.props.propertyHelpText !== undefined ? 
            this.props.propertyHelpText : "")
     }
     handlePropertyTextChange = (value) => {
        this.setState(() => ({
            propertyHelpText: value
        }))
     }
     render() {
         return (
            <React.Fragment>
                    <a className="row-title">
                       { this.state.propertyHelpText }
                    </a>
                    <br />
                    <table className="wl-container wl-container-full wl-spaced-table">
                        <tbody>
                            <tr>
                                <td colspan="2">
                                    Property Help Text
                                </td>
                                <td>
                                    <input type="text"
                                    placeholder="Telephone"
                                    className="wl-form-control wl-property-help-text"
                                    value={this.state.propertyHelpText}
                                    onChange={event=> this.handlePropertyTextChange(event.target.value)}
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    Field Type Help Text
                                </td>
                                <td>
                                    <select className="wl-form-select">
                                        <option value="-1">Custom Field</option>
                                    </select> 
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    Field Help Text
                                </td>
                                <td>
                                    <input type="text" placeholder="Contact Form" className="wl-form-control" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                Transform Help Text
                                </td>
                                <td>
                                    <select className="wl-form-select">
                                        <option value="-1">None</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                                <td>
                                    <button className="button action bg-primary text-white">
                                        Close Mapping
                                    </button>
                                </td>
                            </tr>
                    </tbody></table>
                    <div className="wl-text-right">
                        <br /><br />
                            <button className="wl-add-mapping-button button action bg-primary text-white">
                                Add Mapping
                            </button>
                    </div>


            </React.Fragment>
        )
     }
 }

 // supply a property object as data
 PropertyComponent.propTypes = {
     propertyData: PropTypes.object
 }

 export default PropertyComponent