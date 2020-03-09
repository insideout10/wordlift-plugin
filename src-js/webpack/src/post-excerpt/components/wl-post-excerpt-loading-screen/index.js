/**
 * WlPostExcerptLoadingScreen shows the loading screen when the excerpt is being generated.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * External dependencies.
 */
import React from "react";
/**
 * Internal dependencies.
 */
import Spinner from "../../../common/components/spinner/index";
import {WlContainer} from "../../../mappings/blocks/wl-container";
import {POST_EXCERPT_LOCALIZATION_OBJECT_KEY} from "../../constants";
import {WlColumn} from "../../../mappings/blocks/wl-column";

class WlPostExcerptLoadingScreen extends React.Component {
  constructor(props) {
    super(props);
    this.generatingText = window[POST_EXCERPT_LOCALIZATION_OBJECT_KEY]["generatingText"];
  }
  render() {
    return (
      <React.Fragment>
        <WlContainer fullWidth={true}>
          <WlColumn className={"wl-col--align-center wl-col--full-width"}>
            <Spinner running={true} />
            <br />
            <p>{this.generatingText}</p>
            <br />
          </WlColumn>
        </WlContainer>
      </React.Fragment>
    );
  }
}

export default WlPostExcerptLoadingScreen;
