import React from "react";


/**
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.30.0
 */
export const Sort = ({sortAscHandler, sortDescHandler}) => {
    return (
        <span><a href={"#"}><span className={"dashicons dashicons-arrow-up"} style={{width: "15px"}} onClick={sortAscHandler}></span></a><a
            href={"#"}><span className={"dashicons dashicons-arrow-down"}  style={{width: "15px", marginLeft: "0px"}} onClick={sortDescHandler}></span></a></span>);

}