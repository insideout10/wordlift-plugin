/**
 * Components: Localization Component.
 *
 * @since 3.12.0
 */

/**
 * External dependencies
 */
import React, { Component } from 'react';
import PropTypes from 'prop-types';

/**
 * Extend a component to support localization.
 *
 * @since 3.12.0
 *
 * @param {Object} ComponentToWrap The component to wrap.
 * @returns {LocalizationComponent} The wrapped component.
 */
const localization = ( ComponentToWrap ) => {
	return class LocalizationComponent extends Component {
		// let’s define what’s needed from the `context`
		static contextTypes = {
			l10n: PropTypes.object.isRequired
		};

		/**
		 * @inheritDoc
		 */
		render() {
			const { l10n } = this.context;
			return (
				<ComponentToWrap { ...this.props } l10n={ l10n } />
			);
		}
	};
};

// Finally export the `localization` function.
export default localization;
