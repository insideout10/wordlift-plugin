/**
 * QuestionInputBox for adding a new question
 * @since 3.26.0
 * @author Naveen Muthusamy
 *
 */

/**
 * External dependencies.
 */
import React from 'react'
import {connect} from 'react-redux'

/**
 * Internal dependencies.
 */
import "./index.scss"

class QuestionInputBox extends React.Component {
    render() {
        return (
            <input
                type={"text"}
                defaultValue={this.props.question}
                className={"question-input-box"}
                placeholder={"Add Your question here"}
            />
        )
    }
}

export default connect(state => ({
    question: state.question
}))(QuestionInputBox)