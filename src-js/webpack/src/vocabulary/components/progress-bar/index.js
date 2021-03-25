import React from "react";

export const ProgressBar = ({progress}) => {
    return (
            <div style={{width: "100%", height: "10px", backgroundColor: "#eee"}}>
                <div style={{
                    width: progress + "%",
                    height: "10px",
                    backgroundColor: "green",
                    textAlign: "center"
                }}>
                </div>
            </div>
    )
}