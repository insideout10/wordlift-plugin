/**
 * @since 3.24.0
 * 
 * PropertyListComponent : used to display list of properties present
 * in a mapping item, the user can edit, add, delete properties
 */

import React from 'react'
import PropTypes from 'prop-types';

class PropertyListComponent extends React.Component {
    constructor(props){
        super(props)
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
                            { /** if opened by user then show the property
                             * component, if not then show the property
                             * list item component
                             */}                    
                            <tr>
                                <td className="wl-check-column">
                                <input type="checkbox" />
                                </td>
                                <td>

                                </td>
                                <td />
                            </tr>
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