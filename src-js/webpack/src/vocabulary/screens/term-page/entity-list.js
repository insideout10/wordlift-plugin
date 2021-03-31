/**
 * External dependencies
 */
import React from "react";
import {connect} from "react-redux";
/**
 * Internal dependencies.
 */
import Entity from "../../components/entity";
import {entityAccepted, entityRejected} from "./actions";


const entitySelectedListener = props => {
    // Fire inverse actions since the isActive state is set by backend.
    // when the entity is already active we fire the entity rejected action.
    const data = {
        entityData: props,
        entityIndex: props.entityIndex
    }
    if (props.isActive) {
        props.dispatch(
            entityRejected(data)
        );
    } else {
        props.dispatch(
            entityAccepted(data)
        );
    }
};

class EntityList extends React.Component {

    constructor(props) {
        super(props);
    }

    render() {

        return (
            <React.Fragment>
                {this.props.entities && this.props.entities.map((entity, index) => (
                    <Entity {...entity} entityIndex={index} onEntitySelectedListener={entitySelectedListener}/>
                ))}
            </React.Fragment>
        )
    }
}

export default connect(state => ({
    entities: state.entities,
}))(EntityList);