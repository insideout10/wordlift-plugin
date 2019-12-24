/**
 * BulkActionComponent : Displays the list of bulk actions 
 * based on the category
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

/**
 * External dependencies
 */
import React from "react"
import { BulkActionOptions } from "./BulkActionSubComponents"

class BulkActionComponent extends React.Component {
    render() {
        return (
            <React.Fragment>
                <div className="wl-col">
                    <select className="form-control">
                        <option value="-1">Bulk Actions</option>
                        <BulkActionOptions 
                            choosenCategory={this.props.choosenCategory} 
                        />
                    </select>
                </div>
                <div className="wl-col">
                    <button className="button action" onClick={
                        this.props.bulkActionSubmitHandler()
                    }> 
                        Apply
                    </button>
                </div>
            </React.Fragment>
        )
    }
}

export default BulkActionComponent