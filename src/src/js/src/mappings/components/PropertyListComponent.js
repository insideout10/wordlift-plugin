/**
 * @since 3.24.0
 * 
 * PropertyListComponent : used to display list of properties present
 * in a mapping item, the user can edit, add, delete properties
 */

import React from 'react'
import PropTypes from 'prop-types';
import PropertyComponent from './PropertyComponent';
import PropertyListItemComponent from './PropertyListItemComponent';

class PropertyListComponent extends React.Component {
    constructor(props){
        super(props)
    }
    state = {
        propertyList: (this.props.propertyList !== undefined ?
            this.props.propertyList : [])  
    }
    renderListComponentBasedOnState = (property)=> {
        if (property.isOpenedOrAddedByUser) {
            return (
                // show the property in edit mode
                <PropertyComponent {... property} />
            )
        }
        // if it is not opened then return the list item
        return (
            <PropertyListItemComponent propertyText={property.propertyHelpText} />
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

                            this.state.propertyList.map((property, index) => {

                                return (
                                    <tr className="wl-property-list-item-container">
                                            <td className="wl-check-column">
                                            <input type="checkbox" />
                                            </td>
                                            <td>
                                                { 
                                                    this.renderListComponentBasedOnState(property)
                                                }
                                            </td>
                                            <td />
                                        </tr>
                                )
                            })
                        }   

                        </tbody>
                        </table>          
                        <div className="wl-text-right">
                            <br /><br />
                            <button className="button action bg-primary text-white" style={{margin: 'auto'}}>
                            Add Mapping
                            </button>
                        </div>


            </React.Fragment>
        )
    }
}

PropertyListComponent.propTypes = {
    propertyList: PropTypes.array
}

export default PropertyListComponent