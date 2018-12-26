/**
 * Load the Classification Box.
 *
 * @since 3.20.0
 */
/*
 * External dependencies.
 */
import Provider from "react-redux/es/components/Provider";

/*
 * Internal dependencies.
 */
import store from "./store"
import Wrapper from "../Edit/components/App/Wrapper"
import Header from "../Edit/components/Header"
import VisibleEntityList from "../Edit/containers/VisibleEntityList"
import { receiveAnalysisResults } from "../Edit/actions"

const { select } = wp.data;

const ClassificationBox = () => (
  <Provider store={store}>
    <Wrapper>
      <Header />
      <VisibleEntityList />
    </Wrapper>
  </Provider>
)

const ReceiveAnalysisResultsEvent = (JSONData) => {
  return function (dispatch) {
    // Asynchronously call the dispatch. We need this because we
    // might be inside a reducer call.
      return wp.apiFetch({ 
        url: '/wp-admin/admin-ajax.php?action=wordlift_analyze',
        method: 'POST',
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(JSONData)
      }).then(function(response){
        console.log(response);
        dispatch(receiveAnalysisResults(response));
      });
  }
}

export {
  ClassificationBox,
  ReceiveAnalysisResultsEvent
}