/* globals wlSettings, wp */
/**
 * Load the Classification Box.
 *
 * @since 3.20.0
 */
/*
 * External dependencies.
 */
import { Provider } from "react-redux";

/*
 * Internal dependencies.
 */
import Store1 from "./stores/Store1";
import Wrapper from "../Edit/components/App/Wrapper";
import Header from "../Edit/components/Header";
import VisibleEntityList from "../Edit/containers/VisibleEntityList";
import AddEntity from "../Edit/components/AddEntity";
import { receiveAnalysisResults, setCurrentAnnotation } from "../Edit/actions";
import * as Constants from "./constants";

const canCreateEntities =
  "undefined" !== wlSettings["can_create_entities"] && "yes" === wlSettings["can_create_entities"];

const ClassificationBox = () => (
  <Provider store={Store1}>
    <Wrapper>
      <AddEntity showCreate={canCreateEntities} />
      <Header />
      <VisibleEntityList />
    </Wrapper>
  </Provider>
);

let disambiguated = [];

const ModifyResponse = (response, blockClientId) => {
  // Check for existing entities in store
  let existingEntities = Store1.getState().entities;
  if (existingEntities.size > 0) {
    let entitiesToMerge = existingEntities.toJS();
    for (var entityToMerge in entitiesToMerge) {
      if (response.entities[entityToMerge]) {
        response.entities[entityToMerge].annotations = entitiesToMerge[entityToMerge].annotations;
      } else {
        response.entities[entityToMerge] = entitiesToMerge[entityToMerge];
      }
    }
  }

  // Copy annotations data to respective entities
  for (var annotation in response.annotations) {
    response.annotations[annotation].entityMatches.forEach(entity => {
      if (typeof response.entities[entity.entityId].annotations === "undefined") {
        response.entities[entity.entityId].annotations = {};
      }
      response.entities[entity.entityId].annotations[annotation] = response.annotations[annotation];
      response.entities[entity.entityId].annotations[annotation].blockClientId = blockClientId;
    });
  }

  let block = wp.data.select("core/editor").getBlock(blockClientId);

  // Populate an array of all disambiguated
  if (block.attributes && block.attributes.content) {
    let content = block.attributes.content;
    let contentElem = document.createElement("div");
    contentElem.innerHTML = content;
    if (contentElem.querySelectorAll(".textannotation.disambiguated")) {
      contentElem.querySelectorAll(".textannotation.disambiguated").forEach((nodeValue, nodeIndex) => {
        disambiguated.push(nodeValue.innerText);
      });
    }
  }

  // Update entity occurrences based on disambiguated
  for (var entity in response.entities) {
    response.entities[entity].id = response.entities[entity].entityId;
    let allAnnotations = Object.keys(response.entities[entity].annotations);
    allAnnotations.forEach((annValue, annIndex) => {
      if (disambiguated.includes(response.entities[entity].annotations[annValue].text)) {
        response.entities[entity].occurrences.push(annValue);
      }
    });
  }

  return response;
};

const AnnonateContent = (response, blockClientId) => {
  for (var annotation in response.annotations) {
    wp.data.dispatch("core/annotations").__experimentalAddAnnotation({
      source: Constants.PLUGIN_NAMESPACE,
      id: response.annotations[annotation].annotationId,
      richTextIdentifier: "content",
      blockClientId: blockClientId,
      range: {
        start: response.annotations[annotation].start,
        end: response.annotations[annotation].end
      }
    });
  }
};

const PersistantlyAnnonateContent = (response, blockClientId) => {
  let currentBlock = wp.data.select("core/editor").getBlock(blockClientId);
  let html = currentBlock.attributes.content;
  let blockUid = currentBlock.clientId;
  let value = wp.richText.create({
    html
  });
  for (var annotation in response.annotations) {
    let annotationData = response.annotations[annotation];
    let entityData = response.entities[annotationData.entityMatches[0].entityId];
    let format = {
      type: "span",
      attributes: {
        id: annotationData.annotationId,
        class: `textannotation wl-${entityData.mainType}`,
        itemid: entityData.entityId
      }
    };
    if (disambiguated.includes(annotationData.text)) {
      format.attributes.class += " disambiguated";
    }
    value = wp.richText.applyFormat(value, format, annotationData.start, annotationData.end);
  }
  wp.data.dispatch("core/editor").updateBlock(blockUid, {
    attributes: {
      content: wp.richText.toHTMLString({
        value
      })
    }
  });
};

const ReceiveAnalysisResultsEvent = (JSONData, blockClientId) => {
  return function(dispatch) {
    // Asynchronously call the dispatch. We need this because we
    // might be inside a reducer call.
    wp.apiFetch({
      url: "/wp-admin/admin-ajax.php?action=wordlift_analyze",
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(JSONData)
    }).then(function(response) {
      if (Object.keys(response.entities).length > 0) {
        let modifiedResponse = ModifyResponse(response, blockClientId);
        PersistantlyAnnonateContent(modifiedResponse, blockClientId);
        console.log(`An analysis has been performed for block ${blockClientId}`);
        //AnnonateContent(modifiedResponse, blockClientId);
        dispatch(receiveAnalysisResults(modifiedResponse));
      } else {
        console.log(`No entities found in block ${blockClientId}`);
      }
    });
  };
};

const AnnotateSelected = (start, end) => {
  return function(dispatch) {
    let selectedBlock = wp.data.select("core/editor").getSelectedBlock();
    if (!selectedBlock) return;

    let existingEntities = Store1.getState().entities;
    let annotationId = undefined;

    if (existingEntities.size > 0) {
      let entitiesToCheck = existingEntities.toJS();

      for (var entity in entitiesToCheck) {
        let currEntity = entitiesToCheck[entity];
        for (var annotation in currEntity.annotations) {
          let currAnnotation = currEntity.annotations[annotation];
          if (
            currAnnotation &&
            currAnnotation.blockClientId === selectedBlock.clientId &&
            currAnnotation.start <= start &&
            currAnnotation.end >= end
          ) {
            annotationId = currAnnotation.annotationId;
          }
        }
      }

      dispatch(setCurrentAnnotation(annotationId));
    }
  };
};

export { ClassificationBox, ReceiveAnalysisResultsEvent, AnnotateSelected };
