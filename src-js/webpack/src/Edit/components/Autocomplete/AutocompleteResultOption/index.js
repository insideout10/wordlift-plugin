/**
 * Components: AutocompleteResultOption.
 *
 * A stateless component to display Autocomplete Results in the AutocompleteSelect.
 *
 * @since 1.0.0
 */

/**
 * External dependencies.
 */
import React from 'react'

/**
 * Internal dependencies.
 */
import Wrapper from './Wrapper'
import Image from './Image'
import DisplayTypes from './DisplayTypes'
import Description from './Description'
import FontAwesome from '../FontAwesome'
import Label from './Label'
import Block from './Block'

/**
 *
 * @param {array} images An array of images.
 * @param {array} labels An array of labels.
 * @param {string} scope The result scope: local, network or cloud.
 * @param {string} displayTypes The schema.org types string.
 * @param descriptions
 * @constructor
 */
const AutocompleteResultOption = ({images, scope, displayTypes, descriptions, label}) => {
  return (
    <Wrapper>
      <Image src={images && images[0]}/>
      <Block>
        <Label>{label}</Label>
        <DisplayTypes>{displayTypes}</DisplayTypes>
        <Description>{descriptions && descriptions[0]}</Description>
      </Block>
      {'local' !== scope && <FontAwesome align="right" className="fa fa-cloud"/>}
    </Wrapper>
  )
}

// Finally export the `AutocompleteResultOption`.
export default AutocompleteResultOption
