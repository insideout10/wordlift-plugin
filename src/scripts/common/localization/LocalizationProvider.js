/**
 * Common Modules: Localization Provider.
 *
 * The LocalizationProvider is a React Component which defines a context with
 * localization messages.
 *
 * See
 * * https://facebook.github.io/react/docs/context.html
 * * https://medium.com/@bloodyowl/ _
 * 		the-provider-and-higher-order-component-patterns-with-react-d16ab2d1636
 *
 * @since 3.12.0
 */

/**
 * External dependencies
 */
import { Component, Children } from 'react';
import PropTypes from 'prop-types';

/**
 * Define the LocalizationProvider class.
 *
 * @since 3.12.0
 */
class LocalizationProvider extends Component {
	static propTypes = {
		l10n: PropTypes.object.isRequired
	};

	// you must specify what you’re adding to the context
	static childContextTypes = {
		l10n: PropTypes.object.isRequired,
	};

	/**
	 * @inheritDoc
	 */
	render() {
		return Children.only( this.props.children );
	}

	/**
	 * @inheritDoc
	 */
	getChildContext() {
		// Get the `l10n` prop.
		const { l10n } = this.props;

		// Return the `l10n` prop.
		return { l10n };
	}
}

export default LocalizationProvider;
