/**
 * FaqModal shows the apply list.
 * @since 3.26.0
 * @author Naveen Muthusamy
 *
 */
/**
 * External dependencies.
 */
import React from 'react';
import { connect } from 'react-redux';
/**
 * Internal dependencies.
 */
import {
	requestGetFaqItems,
	updateFaqModalVisibility,
	updateNotificationArea,
} from '../../actions';
import './index.scss';
import { WlModal } from '../../../common/components/wl-modal';
import { WlModalHeader } from '../../../common/components/wl-modal/wl-modal-header';
import { WlModalBody } from '../../../common/components/wl-modal/wl-modal-body';
import FaqApplyList from '../faq-apply-list';
import { WlBgModal } from '../../../common/components/wl-bg-modal';
import WlNotificationArea from '../../../common/components/wl-notification-area';

class FaqModal extends React.Component {
	componentDidMount() {
		this.props.dispatch(requestGetFaqItems());
		this.removeNotificationListener = this.removeNotificationListener.bind(
			this
		);
	}
	/**
	 * Run this listener once the close button is clicked
	 */
	removeNotificationListener() {
		const action = updateNotificationArea({
			notificationMessage: '',
			notificationType: '',
		});
		this.props.dispatch(action);
	}
	render() {
		return (
			<React.Fragment>
				<WlNotificationArea
					notificationMessage={this.props.notificationMessage}
					notificationType={this.props.notificationType}
					notificationCloseButtonClickedListener={
						this.removeNotificationListener
					}
					autoHide={true}
				/>
				<WlBgModal shouldOpenModal={this.props.isModalOpened}>
					<WlModal shouldOpenModal={this.props.isModalOpened}>
						<WlModalHeader
							title={'WordLift FAQ'}
							description={'Apply this answer to a question'}
							modalCloseClickedListener={() => {
								this.props.dispatch(
									updateFaqModalVisibility(false)
								);
							}}
						/>
						<WlModalBody>
							<FaqApplyList />
						</WlModalBody>
					</WlModal>
				</WlBgModal>
			</React.Fragment>
		);
	}
}
export default connect((state) => ({
	isModalOpened: false, // state.faqModalOptions.isModalOpened,
	notificationMessage: '', // state.faqNotificationArea.notificationMessage,
	notificationType: null, // state.faqNotificationArea.notificationType,
}))(FaqModal);
