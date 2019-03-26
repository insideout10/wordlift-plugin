/**
 * External dependencies.
 */
import React from "react";
import { Provider } from "react-redux";

import ButtonContainer from "../../../../Edit/components/AddEntity/ButtonContainer";
import EntitySelectContainer from "../../../../Edit/components/AddEntity/EntitySelectContainer";
import WrapperContainer from "../../../../Edit/components/AddEntity/WrapperContainer";
import Arrow from "../../../../Edit/components/Arrow";

// store passed from props for Gutenberg
const AddEntity = props => (
  <Provider store={props.store}>
    <WrapperContainer>
      <ButtonContainer>
        <Arrow height="8px" color="white" />
      </ButtonContainer>
      <EntitySelectContainer showCreate={props.showCreate} />
    </WrapperContainer>
  </Provider>
);

export default AddEntity;
