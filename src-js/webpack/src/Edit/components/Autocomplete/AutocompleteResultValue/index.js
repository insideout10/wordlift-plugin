/**
 * Components: AutocompleteResultValue.
 *
 * A stateless component to display Autocomplete Results in the AutocompleteSelect.
 *
 * @since 1.0.0
 */
/**
 * External dependencies.
 */
import React, { Component } from 'react'

/**
 * Internal dependencies.
 */
import Wrapper from './Wrapper'
import Label from './Label'
import FontAwesome from '../FontAwesome'

/**
 *
 * @param {array} images An array of images.
 * @param {array} labels An array of labels.
 * @param {string} scope The result scope: local, network or cloud.
 * @param {string} displayTypes The schema.org types string.
 * @param descriptions
 * @constructor
 */
class AutocompleteResultValue extends Component {
  constructor (props) {
    super(props)

    this.onRemove = this.onRemove.bind(this)
  }

  onRemove (event) {
    event.preventDefault()
    event.stopPropagation()
    this.props.onRemove(this.props.value)
  }

  render () {
    const {scope, label} = this.props.value

    return (
      <Wrapper>
        <FontAwesome className="fa fa-minus-square" onMouseDown={this.onRemove}/>
        <Label>{label}</Label>
        {'local' !== scope && <FontAwesome align="right" className="fa fa-cloud"/>}
      </Wrapper>
    )
  }
}

// Finally export the `AutocompleteResultValue`.
export default AutocompleteResultValue
