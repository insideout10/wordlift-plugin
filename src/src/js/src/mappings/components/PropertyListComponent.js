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

class PropertyListComponent extends React.Component {
    constructor(props){
        super(props)
    }
    state = {
        propertyList: (this.props.propertyList !== undefined ?
            this.props.propertyList : [])  
    }
    handleCloseOrOpenPropertyBasedOnState=(index, propData)=> {
        const propertyList = [... this.state.propertyList]
        if (propData != null){
            propertyList[index] = propData
        }
        //invert the state
        propertyList[index].isOpenedOrAddedByUser = !propertyList[index].isOpenedOrAddedByUser
        this.setState({
            propertyList: propertyList
        })
    }
    // triggered when the add mapping button is clicked
    handleAddMappingClick=()=> {
        const newPropertyData = {
            isOpenedOrAddedByUser: true,
            propertyHelpText:"",
            fieldTypeHelpText: "",
            fieldHelpText: "",
            transformHelpText: ""
        }
        this.setState(prevState => ({
            propertyList: [...prevState.propertyList, newPropertyData]
        }))
    }
    renderListComponentBasedOnState = (property, index)=> {
        if (property.isOpenedOrAddedByUser) {
            return (
                // show the property in edit mode
                <PropertyComponent
                propData={property}
                propertyIndex={index}
                switchState={this.handleCloseOrOpenPropertyBasedOnState}/>
            )
        }
        // if it is not opened then return the list item
        return (
            <PropertyListItemComponent
            propertyIndex={index}
            propertyText={property.propertyHelpText}
            switchState={this.handleCloseOrOpenPropertyBasedOnState} />
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

export default PropertyListComponent