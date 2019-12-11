/**
 * EditComponent : it displays the edit section for the mapping item
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

import React from 'react'
import RuleGroupListComponent from './RuleGroupListComponent'
import PropertyListComponent from './PropertyListComponent'
 class EditComponent extends React.Component {

    constructor(props) {
        super(props)
    }
    render() {
        return (
            <React.Fragment>

                <input type="text"
                    className="wl-form-control wl-input-class"
                    size="30"
                    value="My Custom Post Type" />
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
            </React.Fragment>
        )
    }
    
}

export default EditComponent