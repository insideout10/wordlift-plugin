/**
 * Components: Location Component.
 *
 * The location component displays an input with geo-coding enabled to set the
 * `locationCreated` property.
 *
 * @see https://github.com/insideout10/wordlift-plugin/issues/303
 * @see https://github.com/insideout10/wordlift-plugin/issues/586
 *
 * @since 3.15.0
 */

/**
 * Styles.
 *
 * The following style must be loaded as first in order to give precedence to
 * styled-components (which are loaded after).
 */
// import * as style from './style.scss';

/**
 * External dependencies
 */
import React from 'react';

/**
 * Internal dependencies
 */
import Wrapper from './Wrapper';
import Button from './Button';
import Marker from './Marker';
import LocationInput from './LocationInput';

/**
 * Define the {@link Location}.
 *
 * @since 3.11.0
 * @return {Function} The `render` function.
 */
const Location = ( { geoLocation } ) => (
	<Wrapper>
		<Marker geoLocation={ geoLocation } className="fa fa-map-marker fa-5x" />
		<Button geoLocation={ geoLocation } type="button">Get Current Location</Button>
		<LocationInput type="text" value="Get Current Location" />
	</Wrapper>
);

// Finally export the `Location`.
export default Location;
