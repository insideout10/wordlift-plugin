import React from "react";

export const PropertyCloseMappingButton = ({propData, switchState}) => {
    return(
        <tr>
            <td colSpan="2" />
            <td>
                <button
                    disabled={propData.propertyHelpText.length <= 0}
                    className="wl-close-mapping button action bg-primary text-white"
                    onClick={() => switchState(propData.property_id)}
                >
                    Close Mapping
                </button>
            </td>
        </tr>
    )
};