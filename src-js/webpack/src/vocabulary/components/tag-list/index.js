/**
 * External dependencies
 */
import React from "react";
import {connect} from "react-redux";
/**
 * Internal dependencies.
 */
import Tag from "../tag";
import {getTagsAction} from "../../actions";

class TagList extends React.Component {

    constructor(props) {
        super(props);
        this.props.dispatch(getTagsAction({limit: 20}))
    }

    render() {
        return (
            <React.Fragment>
                    {this.props.tags && this.props.tags.map((tag, index) => (
                        <Tag tagIndex={index} tagId={tag.tagId} key={tag.tagId + "-" + index}/>
                    ))}
            </React.Fragment>
        )
    }
}

export default connect(state => ({
    tags: state.tags,
}))(TagList);