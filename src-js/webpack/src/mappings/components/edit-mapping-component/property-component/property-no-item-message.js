/**
 * PropertyNoItemMessage : used to display hint message to user, to indicate there are no
 * properties in the list.
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies
 */
import React from "react";

export const PropertyNoItemMessage = ( {propertyList, chosenCategory }) => {
    return (
        <React.Fragment>
            {0 === propertyList.filter(property => property.propertyStatus === chosenCategory)
                .length && (
                <tr>
                    <td colSpan={2} className="text-center">
                        No Active properties present, click on add new
                    </td>
                </tr>
            )}
        </React.Fragment>

    )
};