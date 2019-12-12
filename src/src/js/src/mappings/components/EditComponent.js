/**
 * EditComponent : it displays the edit section for the mapping item
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

import React from 'react'
import RuleGroupListComponent from './RuleGroupListComponent'
import PropertyListComponent from './PropertyListComponent'
import { connect } from 'react-redux'
import { TITLE_CHANGED_ACTION } from '../actions/actions'

 class EditComponent extends React.Component {

    constructor(props) {
        super(props)
    }
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
                                    <RuleGroupListComponent 
                                        ruleGroupList={[{}]}/>
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
                <div class="wl-container wl-container-full">
                    <div class="wl-col">
                        <select  class="form-control">
                            <option value="-1">Bulk Actions</option>
                            <option value="duplicate">Duplicate</option>
                            <option value="trash">Move to Trash</option>
                        </select>
                    </div>
                    <div class="wl-col">
                        <button class="button action"> Apply </button>
                    </div>
                    <div class="wl-col wl-align-right">
                        <button class="button action"> Save </button>
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