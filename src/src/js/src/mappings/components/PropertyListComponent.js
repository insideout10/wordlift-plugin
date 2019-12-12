/**
 * PropertyListComponent : used to display list of properties present
 * in a mapping item, the user can edit, add, delete properties
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

import React from 'react'
import PropTypes from 'prop-types';
import PropertyComponent from './PropertyComponent';
import PropertyListItemComponent from './PropertyListItemComponent';
import { connect } from 'react-redux'
import { OPEN_OR_CLOSE_PROPERTY_ACTION, ADD_MAPPING_ACTION } from '../actions/actions';

class PropertyListComponent extends React.Component {
    constructor(props){
        super(props)
    }
     /**
      * It makes property item 
      * switch from edit mode to list item mode and vice versa
      * @param {Number} propertyIndex 
      */
     switchState = ( propertyIndex ) => {
        const action  = OPEN_OR_CLOSE_PROPERTY_ACTION
        action.payload = {
            propertyIndex: propertyIndex
        }
        this.props.dispatch(action)
     }
    // triggered when the add mapping button is clicked
    handleAddMappingClick = ()=> {
        this.props.dispatch( ADD_MAPPING_ACTION )
    }
    renderListComponentBasedOnState = (property, index)=> {
        console.log(this.props)
        console.log(property)
        if (property.isOpenedOrAddedByUser) {
            return (
                // show the property in edit mode
                <PropertyComponent
                propData={property}
                propertyIndex={index}
                switchState={this.switchState}/>
            )
        }
        // if it is not opened then return the list item
        return (
            <PropertyListItemComponent
            propertyIndex={index}
            propertyText={property.propertyHelpText}
            switchState={this.switchState} />
        )
    }
    render() {
        return ( 
            <React.Fragment>
                <table className="wp-list-table widefat striped wl-table wl-container-full">
                        <thead>
                        <tr>
                            <th className="wl-check-column">
                            <input type="checkbox" /> 
                            </th>
                            <th style={{width: '30%'}}>
                            <b>Property</b>
                            </th>
                            <th>
                            <b>Field</b>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                  
                        {

                            this.props.propertyList.map((property, index) => {

                                return (
                                    <tr className="wl-property-list-item-container">
                                            <td className="wl-check-column">
                                            <input type="checkbox" />
                                            </td>
                                            <td>
                                                { 
                                                    this.renderListComponentBasedOnState(property, index)
                                                }
                                            </td>
                                            <td />
                                        </tr>
                                )
                            })
                        }   
                        <tr className="wl-text-right">
                           <td />
                           <td />
                            <td><br />
                                <button
                                className="button action bg-primary text-white wl-add-mapping"
                                style={{margin: 'auto'}}
                                onClick={this.handleAddMappingClick}>
                                Add Mapping
                                </button> <br />
                            </td>
                        </tr>
                    </tbody>
                </table>          
            </React.Fragment>
        )
    }
}

PropertyListComponent.propTypes = {
    propertyList: PropTypes.array
}

const mapStateToProps = function( state ) {
    return {
        propertyList: state.PropertyListData.propertyList
    }
}

export default connect(mapStateToProps)(PropertyListComponent)