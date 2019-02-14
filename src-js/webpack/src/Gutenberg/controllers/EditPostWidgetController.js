/* globals wp */
/*
 * Internal dependencies.
 */
import Store1 from "../stores/Store1";
import { updateOccurrencesForEntity } from "../../Edit/actions";

class EditPostWidgetController {
  static onSelectedEntityTile(entity) {
    var action;
    action = "entitySelected";
    if (entity.occurrences.length > 0) {
      action = "entityDeselected";
    }
    console.log(`Action '${action}' on entity ${entity.id} within ${entity.mainType}`);
    console.log(`Calculating occurrences for entity ${entity.id}...`);
    let occurrences = [];
    if (action === "entitySelected") {
      for (var annotation in entity.annotations) {
        EditPostWidgetController.disambiguate(annotation, true);
        occurrences.push(annotation);
      }
    } else {
      for (var annotation in entity.annotations) {
        EditPostWidgetController.disambiguate(annotation, false);
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

// Finally export the singleton class.
export default EditPostWidgetController;
