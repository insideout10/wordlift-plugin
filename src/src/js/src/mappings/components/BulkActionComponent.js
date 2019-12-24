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

const BulkActionComponent = (props) => {

    return (
        <React.Fragment>
            <div className="wl-col">
                <select className="form-control">
                    <option value="-1">Bulk Actions</option>
                    {
                        props.options.map( ( item, index ) => {

                            return (
                                <option value={item.value}>
                                    { item.label }
                                </option>
                            )

                        })
                    }
                </select>
            </div>
            <div className="wl-col">
                <button className="button action" onClick={
                    props.bulkActionSubmitHandler()
                }> 
                    Apply
                </button>
            </div>
        </React.Fragment>
    )


}

export default BulkActionComponent