import React from "react";

function AscendingSort() {

    return <span className={"dashicons dashicons-arrow-up"} > </span>;
}

function DescendingSort() {
    return <span className={"dashicons dashicons-arrow-down"}> </span>;
}

/**
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.30.0
 */
export const Sort = ({isAscending}) => {

    if (isAscending) {
        return AscendingSort();
    }
    return DescendingSort();
}