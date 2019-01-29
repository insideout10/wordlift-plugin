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
import AddEntity from "../Edit/components/AddEntity";
import { receiveAnalysisResults } from "../Edit/actions"

const canCreateEntities = "undefined" !== wlSettings["can_create_entities"] && "yes" === wlSettings["can_create_entities"];

/*
 * Packages via WordPress global
 */
const { select, dispatch } = wp.data;

const PLUGIN_NAMESPACE = "wordlift";

window.store1 = store;

const ClassificationBox = () => (
  <Provider store={store}>
    <Wrapper>
      <AddEntity showCreate={canCreateEntities} />
      <Header />
      <VisibleEntityList />
    </Wrapper>
  </Provider>
)

let disambiguated = [];
let disambiguatedAnnotations = [];

const ModifyResponse = (response, blockIndex) => {

  for (var annotation in response.annotations) {
    response.annotations[annotation].entityMatches.forEach(entity => {
      if (typeof response.entities[entity.entityId].annotations === 'undefined') {
        response.entities[entity.entityId].annotations = {};
      }
      response.entities[entity.entityId].annotations[annotation] = response.annotations[annotation];
      response.entities[entity.entityId].annotations[annotation].blockIndex = blockIndex;
    });
  }

  let block = wp.data.select( "core/editor" ).getBlocks()[blockIndex];
  if(block.attributes && block.attributes.content){
    let content = block.attributes.content;
    let contentElem = document.createElement('div');
    contentElem.innerHTML = content;
    if (contentElem.querySelectorAll('.textannotation.disambiguated')) {
      contentElem.querySelectorAll('.textannotation.disambiguated').forEach((nodeValue, nodeIndex) => { 
        disambiguated.push(nodeValue.innerText);
      })
    }
  }

  for (var entity in response.entities) {
    response.entities[entity].id = response.entities[entity].entityId;
    let allAnnotations = Object.keys(response.entities[entity].annotations);
    allAnnotations.forEach((annValue, annIndex) => { 
      if(disambiguated.includes(response.entities[entity].annotations[annValue].text)){
        console.log(response.entities[entity]);
        response.entities[entity].occurrences.push(annValue);
        disambiguatedAnnotations.push(response.entities[entity].annotations[annValue]);
      }
    })
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

  let currentBlock = wp.data.select( "core/editor" ).getBlocks()[blockIndex];
  let html = currentBlock.attributes.content;
  let blockUid = currentBlock.clientId;
  let value = wp.richText.create({
    html
  });
  for (var annotation in response.annotations) {
    let annotationData = response.annotations[annotation];
    let entityData = response.entities[annotationData.entityMatches[0].entityId];
    let format = {
      type: 'span',
      attributes: {
        id: annotationData.annotationId,
        class: `textannotation wl-${entityData.mainType}`,
        itemid: entityData.entityId
      }
    }
    if(disambiguated.includes(annotationData.text)){
      format.attributes.class += ' disambiguated';
    }
    value = wp.richText.applyFormat(value, format, annotationData.start, annotationData.end);
  }
  wp.data.dispatch( "core/editor" ).updateBlock( blockUid, {
    attributes: {
      content: wp.richText.toHTMLString({
        value
      })
    }
  } );

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
        let modifiedResponse = ModifyResponse(response, blockIndex);
        PersistantlyAnnonateContent(modifiedResponse, blockIndex);
        console.log(`An analysis has been performed for block ${blockIndex}`);
        //AnnonateContent(modifiedResponse, blockIndex);
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