import React from "react";

function AscendingSort(onClickListener) {
    return <span className={"dashicons dashicons-arrow-up"} onClick={onClickListener}> </span>;
}

function DescendingSort(onClickListener) {
    return <span className={"dashicons dashicons-arrow-down"} onClick={onClickListener}> </span>;
}

/**
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.30.0
 */
export const Sort = (isAscending, onClick) => {
    if (isAscending) {
        return AscendingSort(onClick);
    }
    return DescendingSort(onClick);
}