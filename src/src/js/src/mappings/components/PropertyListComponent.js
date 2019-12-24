/**
 * PropertyListComponent : used to display list of properties present
 * in a mapping item, the user can edit, add, delete properties
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

/**
 * External dependencies
 */
import React from 'react'
import { connect } from 'react-redux'
import PropTypes from 'prop-types';

/**
 * Internal dependencies
 */
import PropertyComponent from './PropertyComponent';
import CategoryComponent from './CategoryComponent';
import PropertyListItemComponent from './PropertyListItemComponent';
import { OPEN_OR_CLOSE_PROPERTY_ACTION, ADD_MAPPING_ACTION, PROPERTY_LIST_CHOOSEN_CATEGORY_CHANGED_ACTION } from '../actions/actions';


class PropertyListComponent extends React.Component {
    constructor(props){
        super(props)
        console.log( props )
    }
     /**
      * It makes property item 
      * switch from edit mode to list item mode and vice versa
      * @param {Number} propertyIndex 
      */
     switchState = ( propertyId ) => {
        const action  = OPEN_OR_CLOSE_PROPERTY_ACTION
        action.payload = {
            propertyId: propertyId
        }
        console.log( action )
        this.props.dispatch( action )
     }
    // triggered when the add mapping button is clicked
    handleAddMappingClick = ()=> {
        this.props.dispatch( ADD_MAPPING_ACTION )
    }
    categorySelectHandler = ( category ) => {
        console.log( category )
        const action = PROPERTY_LIST_CHOOSEN_CATEGORY_CHANGED_ACTION
        action.payload = {
            choosenCategory: category
        }
        this.props.dispatch( action )
    }
    /**
     * It Renders depends on the isOpenedOrAddedByUser boolean present
     * in the property object.
     * @param {Object} property A single property present in property list
     * @param {Number} index Index of the property in property list
     */
    renderListComponentBasedOnState = (property, index)=> {
        if (property.isOpenedOrAddedByUser) {
            return (
                // show the property in edit mode
                <PropertyComponent
                propData={property}
                switchState={this.switchState}/>
            )
        }
        // if it is not opened then return the list item
        return (
            <PropertyListItemComponent
            choosenCategory={ this.props.choosenCategory }
            propData={property}
            switchState={this.switchState} />
        )
    }
    render() {
        return ( 
            <React.Fragment>
                <CategoryComponent
                    source                = { this.props.propertyList }
                    categoryKeyName       = 'property_status'
                    categories            = { [ 'active', 'trash' ] }
                    categorySelectHandler = { this.categorySelectHandler }
                    choosenCategory       = { this.props.choosenCategory }
                />
                <br/>
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
                                0 === this.props.propertyList
                                .filter( property => property.property_status === this.props.choosenCategory )
                                .length &&
                                <tr>
                                    <td colSpan={2} className="text-center">
                                        No Active properties present, click on add new
                                    </td>
                                </tr>
                            }               
                            {
                                this.props.propertyList
                                .filter( property => property.property_status === this.props.choosenCategory )
                                .map((property, index) => {
                                    console.log( property )
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

                            <td colspan="3"><br />
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
        propertyList: state.PropertyListData.propertyList,
        choosenCategory: state.PropertyListData.choosenPropertyCategory
    }
}

export default connect(mapStateToProps)(PropertyListComponent)