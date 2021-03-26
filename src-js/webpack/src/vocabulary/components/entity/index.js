/**
 * External dependencies
 */
import React from "react";
import {connect} from "react-redux";
/**
 * Internal dependencies
 */
import "./index.scss"
import {setEntityActive} from "../../actions";

class Entity extends React.Component {

    constructor(props) {
        super(props);
    }

    render() {
        console.log("entity component rendered")
        const entity = this.props
        const selectedCssClass = entity.isActive ? 'card-input__selected' : '';
        if (entity.isHidden) {
            return (<React.Fragment/>)
        }
        return (
            <React.Fragment>
                <table className={"card-input " + selectedCssClass} onClick={() => {
                    this.props.dispatch(setEntityActive({
                        entityIndex: this.props.entityIndex,
                        tagIndex: this.props.tagIndex
                    }))
                }}>
                    <tbody>
                    <tr>
                        <td style={{"width": "90%"}}><p style={{"fontSize": "18px"}}><b>{entity.label}</b> ({entity.mainType})</p></td>
                        <td style={{"width": "10%"}}>{parseFloat(entity.confidence * 100).toFixed(2) }%</td>
                    </tr>
                    {entity.meta.description &&
                    <tr>
                        <td className={"card-input-description"}>{entity.meta.description}</td>
                    </tr>
                    }
                    <tr>
                        <td>
                            {this.showSameAsUris(entity)}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </React.Fragment>
        )
    }


    showSameAsUris(entity) {
        const regex = /https?:\/\/(dbpedia|www\.wikidata)\.org/gm;
        const sameAs = entity.sameAs.filter(e => e.match(regex))
        const sameAsUris = [entity.entityId].concat(sameAs)
        return <p>{sameAsUris.join(", ")}</p>;
    }
}


export default connect()(Entity);