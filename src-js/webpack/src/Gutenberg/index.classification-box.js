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

/*
 * Packages via WordPress global
 */
const { select, dispatch } = wp.data;

const PLUGIN_NAMESPACE = "wordlift";

const ClassificationBox = () => (
  <Provider store={store}>
    <Wrapper>
      <Header />
      <VisibleEntityList />
    </Wrapper>
  </Provider>
)

const ModifyResponse = (response) => {

  for (var entity in response.entities) {
    response.entities[entity].id = response.entities[entity].entityId;
    response.entities[entity].annotations = {};
  }

  for (var annotation in response.annotations) {
    response.annotations[annotation].entityMatches.forEach(entity => {
      response.entities[entity.entityId].annotations[annotation] = response.annotations[annotation];
    });
  }

  return response;
}

const AnnonateContent = (response, blockIndex) => {

  for (var annotation in response.annotations) {

    dispatch( 'core/annotations' ).__experimentalAddAnnotation({
      source: PLUGIN_NAMESPACE,
      id: response.annotations[annotation].annotationId,
      richTextIdentifier: "content",
      blockClientId: select( 'core/editor' ).getBlockOrder()[blockIndex],
      range: {
        start: response.annotations[annotation].start,
        end: response.annotations[annotation].end,
      }
    });
  }

}

const PersistantlyAnnonateContent = (response, blockIndex) => {

  let html = wp.data.select( "core/editor" ).getBlocks()[blockIndex].attributes.content;
  let blockUid = wp.data.select( "core/editor" ).getBlocks()[blockIndex].clientId;
  let value = wp.richText.create({
    html
  });
  for (var annotation in response.annotations) {
    value = wp.richText.applyFormat(value, { type: 'strong' }, response.annotations[annotation].start, response.annotations[annotation].end);
  }
  let block = wp.blocks.createBlock( 'core/paragraph' );
  block.attributes.content = wp.richText.toHTMLString({
    value
  });
  wp.data.dispatch( 'core/editor' ).replaceBlocks( blockUid, block );

}

const ReceiveAnalysisResultsEvent = (JSONData, blockIndex) => {
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
      if (Object.keys(response.entities).length > 0) {
        let modifiedResponse = ModifyResponse(response);
        PersistantlyAnnonateContent(modifiedResponse, blockIndex);
        AnnonateContent(modifiedResponse, blockIndex);
        dispatch(receiveAnalysisResults(modifiedResponse));
      } else {
        console.log(`No entities found in block ${blockIndex}`);
      }
    });
  }
}

export {
  ClassificationBox,
  ReceiveAnalysisResultsEvent
}