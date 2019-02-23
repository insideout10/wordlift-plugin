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
import { receiveAnalysisResults, setCurrentAnnotation, updateOccurrencesForEntity } from "../../Edit/actions";
import { processingBlockAdd, processingBlockRemove } from "../actions";

const canCreateEntities =
  "undefined" !== wlSettings["can_create_entities"] && "yes" === wlSettings["can_create_entities"];

/**
 * Define the `AnnotationService` class.
 *
 * @since 3.2x
 */
class AnnotationService {
  constructor(block) {
    this.block = block;
    this.blockClientId = block.clientId;
    this.blockContent = block.attributes && block.attributes.content;
    this.disambiguated = [];
    this.rawResponse = null;
    this.modifiedResponse = null;
  }

  modifyResponse() {
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

    // Populate an array of all disambiguated
    if (this.block.attributes && this.blockContent) {
      let contentElem = document.createElement("div");
      contentElem.innerHTML = this.blockContent;
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

  persistentlyAnnotate() {
    let richText = wp.richText.create({
      html: this.blockContent
    });
    for (var annotation in this.modifiedResponse.annotations) {
      const annotationData = this.modifiedResponse.annotations[annotation];
      const entityData = this.modifiedResponse.entities[annotationData.entityMatches[0].entityId];
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
      richText = wp.richText.applyFormat(richText, format, annotationData.start, annotationData.end);
    }
    wp.data.dispatch("core/editor").updateBlock(this.blockClientId, {
      attributes: {
        content: wp.richText.toHTMLString({
          value: richText
        })
      }
    });
  }

  wordliftAnalyze() {
    let _this = this;
    return function(dispatch) {
      dispatch(processingBlockAdd(_this.blockClientId));
      if (_this.blockContent && _this.block.name != "core/freeform") {
        console.log(`Requesting analysis for block ${_this.blockClientId}...`);
        wp.apiFetch(_this.getWordliftAnalyzeRequest())
          .then(function(response) {
            if (Object.keys(response.entities).length > 0) {
              _this.rawResponse = response;
              _this.modifyResponse();
              _this.persistentlyAnnotate();
              console.log(`An analysis has been performed for block ${_this.blockClientId}`);
              dispatch(receiveAnalysisResults(_this.modifiedResponse));
            } else {
              console.log(`No entities found in block ${_this.blockClientId}`);
            }
            dispatch(processingBlockRemove(_this.blockClientId));
          })
          .catch(function(error) {
            console.log("Error fetching from API: ", error);
            dispatch(processingBlockRemove(_this.blockClientId));
          });
      } else if (_this.block.name === "core/freeform") {
        AnnotationService.classicEditorNotice();
        dispatch(processingBlockRemove(_this.blockClientId));
      } else {
        console.log(`No content found in block ${_this.blockClientId}`);
        dispatch(processingBlockRemove(_this.blockClientId));
      }
    };
  }

  getWordliftAnalyzeRequest() {
    return {
      url: `${wlSettings["ajax_url"]}?action=wordlift_analyze`,
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        contentLanguage: wlSettings.language,
        contentType: "text/html",
        scope: "all",
        version: "1.0.0",
        content: this.blockContent
      })
    };
  }

  static classicEditorNotice() {
    wp.data
      .dispatch("core/notices")
      .createInfoNotice("WordLift content analysis is not compatible with Classic Editor blocks. ", {
        actions: [
          {
            url: "https://wordpress.org/plugins/classic-editor/",
            label: "Switch to Classic Editor"
          },
          {
            url: "https://ithemes.com/wp-content/uploads/2018/12/wordpress-5.0-convert-to-blocks-1024x583.png",
            label: "Convert to Gutenberg Blocks"
          }
        ]
      });
  }

  static convertClassicEditorBlocks() {
    wp.data
      .select("core/editor")
      .getBlocks()
      .forEach(function(block, blockIndex) {
        if (block.name === "core/freeform") {
          wp.data
            .dispatch("core/editor")
            .replaceBlocks(block.clientId, wp.blocks.rawHandler({ HTML: wp.blocks.getBlockContent(block) }));
        }
      });
  }

  static annotateSelected(start, end) {
    return function(dispatch) {
      const selectedBlock = wp.data.select("core/editor").getSelectedBlock();
      if (!selectedBlock) return;

      const existingEntities = Store1.getState().entities;
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

  static onSelectedEntityTile(entity) {
    let action = "entitySelected";
    if (entity.occurrences.length > 0) {
      action = "entityDeselected";
    }
    console.log(`Action '${action}' on entity ${entity.id} within ${entity.mainType}`);
    console.log(`Calculating occurrences for entity ${entity.id}...`);
    let occurrences = [];
    if (action === "entitySelected") {
      for (var annotation in entity.annotations) {
        AnnotationService.disambiguate(annotation, true);
        occurrences.push(annotation);
      }
    } else {
      for (var annotation in entity.annotations) {
        AnnotationService.disambiguate(annotation, false);
      }
    }
    console.log(`Found ${occurrences.length} annotation(s) for entity ${entity.id}.`);
    setTimeout(function() {
      console.log(`Updating ${occurrences.length} occurrence(s) for ${entity.id}...`);
      Store1.dispatch(updateOccurrencesForEntity(entity.entityId, occurrences));
    }, 0);
  }

  static disambiguate(elem, action) {
    const disambiguateClass = "disambiguated";

    wp.data
      .select("core/editor")
      .getBlocks()
      .forEach((block, blockIndex) => {
        if (block.attributes && block.attributes.content) {
          let content = block.attributes.content;
          let blockUid = block.clientId;
          let contentElem = document.createElement("div");
          let selector = elem.replace("urn:", "urn\\3A ");

          contentElem.innerHTML = content;
          if (contentElem.querySelector("#" + selector)) {
            action
              ? contentElem.querySelector("#" + selector).classList.add(disambiguateClass)
              : contentElem.querySelector("#" + selector).classList.remove(disambiguateClass);
            wp.data.dispatch("core/editor").updateBlock(blockUid, {
              attributes: {
                content: contentElem.innerHTML
              }
            });
          }
        }
      });
  }
}

export default AnnotationService;
