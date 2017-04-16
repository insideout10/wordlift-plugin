/**
 * Components: Navigator Widget Tile.
 *
 * This component represent one Tile.
 *
 * @since 3.12.0
 */

/**
 * External dependencies
 */
import React from 'react';

/**
 * Internal dependencies
 */
import Wrapper from './Wrapper';
import Header from '../Header';
import Thumbnail from '../Thumbnail';
import Title from '../Title';
import Excerpt from '../Excerpt';
import More from '../More';
import localization from '../../../common/localization/LocalizationComponent';

/**
 * Define the {@link App}.
 *
 * @since 3.12.0
 * @return {Function} The `render` function.
 */
const Tile = ( { entity, post, l10n } ) => (
	<Wrapper className={ entity.mainType }>
		<Header entity={ entity } />
		<Thumbnail source={ post.thumbnail } />
		<Title link={ post.permalink }>{ post.title }</Title>
		<Excerpt>{ post.excerpt }</Excerpt>
		<More link={ post.permalink }>{ l10n[ 'Read More' ]}</More>
	</Wrapper>
);

// Finally export the `Tile`.
export default localization( Tile );
