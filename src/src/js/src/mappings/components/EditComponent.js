/**
 * EditComponent : it displays the edit section for the mapping item
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

/**
 * External dependencies
 */
import React from 'react'
import { connect } from 'react-redux'

/**
 * Internal dependencies
 */
import RuleGroupListComponent from './RuleGroupListComponent'
import PropertyListComponent from './PropertyListComponent'
import { TITLE_CHANGED_ACTION } from '../actions/actions'

 class EditComponent extends React.Component {

    constructor(props) {
        super(props)
    }
    /**
     * When the title is changed, this method saves it in the redux store.
     * @param {Object} event The event which is fired when mapping title changes
     */
    handleTitleChange = (event)=> {
        const action = TITLE_CHANGED_ACTION
        action.payload = {
            value: event.target.value
        }
        this.props.dispatch(action)
    }
    render() {
        return (
            <React.Fragment>
                 <br /> <br />
                <input type="text"
                    className="wl-form-control wl-input-class"
                    defaultValue={this.props.title}
                    onChange={(e)=> {this.handleTitleChange(e)}}/>
                    <br /> <br />
                <table className="wp-list-table widefat striped wl-table wl-container-full">
                    <thead>
                    <tr>
                        <td colSpan={0}>
                        <b>Rules</b> 
                        </td>
                        <td colSpan={2}>
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td className="wl-bg-light wl-description">
                            Here we show the help text
                            </td>
                            <td>
                                <div>
                                    <b>Use the mapping if</b>
                                    <RuleGroupListComponent />
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <br/><br/>
                <PropertyListComponent />
                <br/>
                <div className="wl-container wl-container-full">
                    <div className="wl-col">
                        <select  className="form-control">
                            <option value="-1">Bulk Actions</option>
                            <option value="duplicate">Duplicate</option>
                            <option value="trash">Move to Trash</option>
                        </select>
                    </div>
                    <div className="wl-col">
                        <button className="button action"> Apply </button>
                    </div>
                    <div className="wl-col wl-align-right">
                        <button className="button action"> Save </button>
                    </div>
                </div>
            </React.Fragment>
        )
    }
}

const mapStateToProps = function( state ) {
    return {
        title: state.TitleSectionData.title
    }
}

export default connect(mapStateToProps)(EditComponent)