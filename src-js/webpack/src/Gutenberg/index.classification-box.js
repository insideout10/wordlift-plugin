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

const getContentLength = (blockForSerialization) => {

  if (blockForSerialization.name === 'core/paragraph'){
    return jQuery(blockForSerialization.originalContent.trim().replace(/(\r\n\t|\n|\r\t)/gm, "")).text().length;
  } else {
    return blockForSerialization.originalContent.trim().replace(/(\r\n\t|\n|\r\t)/gm, "").length;
  }

}

const getOffset = (blockOffset, start) => {

  for(var i = blockOffset.length - 1; i > 0; i--){
    if( i === blockOffset.length - 1 && start >= blockOffset[i].offset ) {
      return {
        index: i,
        offset: blockOffset[i - 1].offset
      }
    }
    if( start < blockOffset[i].offset && start >= blockOffset[i - 1].offset) {
      return {
        index: i - 1,
        offset: blockOffset[i - 1].offset
      }
    }

  }

}

const AnnonateContent = (response) => {

  let blockCount = select( 'core/editor' ).getBlockCount();
  let blockOffset = [];

  for(var i = 0; i < blockCount; i++){
    let blockContentLen = getContentLength(wp.data.select( 'core/editor' ).getBlocksForSerialization()[i]);
    let offset = 0;
    if(i > 0){
      offset = blockOffset[i - 1].offset + blockOffset[i - 1].length;
    }
    blockOffset[i] = {
      length: blockContentLen,
      offset: offset
    }
  }

  for (var annotation in response.annotations) {

    let start = response.annotations[annotation].start;
    let end = response.annotations[annotation].end;

    let toDispatch = {
      source: PLUGIN_NAMESPACE,
      richTextIdentifier: "content",
    }

    let offset = getOffset(blockOffset, start);
    
    toDispatch.blockClientId = wp.data.select( 'core/editor' ).getBlockOrder()[offset.index];
    toDispatch.range = {
      start: start - offset.offset,
      end: end - offset.offset,
    }

    dispatch( 'core/annotations' ).__experimentalAddAnnotation(toDispatch);
  }

}

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
      let modifiedResponse = ModifyResponse(response);
      AnnonateContent(modifiedResponse);
      dispatch(receiveAnalysisResults(modifiedResponse));
    });
  }
}

export {
  ClassificationBox,
  ReceiveAnalysisResultsEvent
}