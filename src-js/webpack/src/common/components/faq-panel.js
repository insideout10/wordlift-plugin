/**
 * External dependencies
 */
import React from 'react';
/**
 * WordPress dependencies
 */
import { Panel, PanelBody } from '@wordpress/components';
import { Button, PopoverManager, withEditorEvents } from '@wordlift/design';
import FaqModal from '../../faq/components/faq-modal';
import FaqScreen from '../../faq/components/faq-screen';

const PopoverWithEditorEvents = withEditorEvents(PopoverManager);
withEditorEvents.register();

export default () => (
	<Panel>
		<PanelBody title="FAQ">
			<FaqScreen />
			<FaqModal />
		</PanelBody>
		<PopoverWithEditorEvents positions={['right', 'bottom', 'top', 'left']}>
			<Button>Click Me</Button>
		</PopoverWithEditorEvents>
	</Panel>
);
