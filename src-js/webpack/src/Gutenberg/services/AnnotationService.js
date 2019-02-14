/* globals wp, wlSettings */
/**
 * Services: Link Service.
 *
 * A service which handles the link/no link attribute for entity's occurrences.
 *
 * @since 3.2x
 */

/*
 * Internal dependencies.
 */
import Store1 from "../stores/Store1";
import { receiveAnalysisResults, setCurrentAnnotation } from "../../Edit/actions";
import * as Constants from "../constants";

const canCreateEntities =
  "undefined" !== wlSettings["can_create_entities"] && "yes" === wlSettings["can_create_entities"];

/**
 * Define the `AnnotationService` class.
 *
 * @since 3.2x
 */
class AnnotationService {
  constructor(content, blockClientId) {
    this.blockClientId = blockClientId;
    this.disambiguated = [];
    this.rawResponse = null;
    this.modifiedResponse = null;
    this.body = {
      contentLanguage: "en",
      contentType: "text/html",
      scope: "all",
      version: "1.0.0",
      content: content
    };
  }

  ModifyResponse() {
    let response = this.rawResponse;

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
        response.entities[entity.entityId].annotations[annotation].blockClientId = this.blockClientId;
      });
    }

    let block = wp.data.select("core/editor").getBlock(this.blockClientId);

    // Populate an array of all disambiguated
    if (block.attributes && block.attributes.content) {
      let content = block.attributes.content;
      let contentElem = document.createElement("div");
      contentElem.innerHTML = content;
      if (contentElem.querySelectorAll(".textannotation.disambiguated")) {
        contentElem.querySelectorAll(".textannotation.disambiguated").forEach((nodeValue, nodeIndex) => {
          this.disambiguated.push(nodeValue.innerText);
        });
      }
    }

    // Update entity occurrences based on disambiguated
    for (var entity in response.entities) {
      response.entities[entity].id = response.entities[entity].entityId;
      let allAnnotations = Object.keys(response.entities[entity].annotations);
      allAnnotations.forEach((annValue, annIndex) => {
        if (this.disambiguated.includes(response.entities[entity].annotations[annValue].text)) {
          response.entities[entity].occurrences.push(annValue);
        }
      });
    }

    this.modifiedResponse = response;
  }

  PersistentlyAnnotateContent() {
    let currentBlock = wp.data.select("core/editor").getBlock(this.blockClientId);
    let html = currentBlock.attributes.content;
    let blockUid = currentBlock.clientId;
    let value = wp.richText.create({
      html
    });
    for (var annotation in this.modifiedResponse.annotations) {
      let annotationData = this.modifiedResponse.annotations[annotation];
      let entityData = this.modifiedResponse.entities[annotationData.entityMatches[0].entityId];
      let format = {
        type: "span",
        attributes: {
          id: annotationData.annotationId,
          class: `textannotation wl-${entityData.mainType}`,
          itemid: entityData.entityId
        }
      };
      if (this.disambiguated.includes(annotationData.text)) {
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
  }

  WordliftAnalyze() {
    let _this = this;
    return function(dispatch) {
      wp.apiFetch({
        url: "/wp-admin/admin-ajax.php?action=wordlift_analyze",
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify(_this.body)
      }).then(function(response) {
        if (Object.keys(response.entities).length > 0) {
          _this.rawResponse = response;
          _this.ModifyResponse();
          _this.PersistentlyAnnotateContent();
          console.log(`An analysis has been performed for block ${_this.blockClientId}`);
          dispatch(receiveAnalysisResults(_this.modifiedResponse));
        } else {
          console.log(`No entities found in block ${_this.blockClientId}`);
        }
      });
    };
  }

  static AnnotateSelected(start, end) {
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
  }
}

export default AnnotationService;
