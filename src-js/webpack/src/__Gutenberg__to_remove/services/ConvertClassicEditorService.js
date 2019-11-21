/* globals wp */

/*
 * Internal dependencies.
 */
import AnnotationService from "./AnnotationService";
import * as Constants from "../constants";

class ConvertClassicEditorService {
  static showNotice() {
    ConvertClassicEditorService.convertBlocks();
    wp.data
      .dispatch("core/notices")
      .createInfoNotice("WordLift content analysis is not compatible with Classic Editor blocks. ", {
        id: Constants.CONVERT_CLASSIC_NOTICE_ID,
        actions: [
          {
            url: "https://wordpress.org/plugins/classic-editor/",
            label: "Switch to Classic Editor"
          },
          {
            url: "javascript:window.wordlift.convertClassicEditorBlocks()",
            label: "Convert to Gutenberg Blocks"
          }
        ]
      });
  }

  static removeNotice() {
    wp.data.dispatch("core/notices").removeNotice(Constants.CONVERT_CLASSIC_NOTICE_ID);
  }

  static convertBlocks() {
    if (window.wordlift.convertClassicEditorBlocks && window.wordlift.AnnotationService) {
      return;
    }
    window.wordlift.AnnotationService = AnnotationService;
    window.wordlift.convertClassicEditorBlocks = function() {
      wp.data
        .select("core/editor")
        .getBlocks()
        .forEach(function(block) {
          if (block.name === "core/freeform") {
            wp.data
              .dispatch("core/editor")
              .replaceBlocks(block.clientId, wp.blocks.rawHandler({ HTML: wp.blocks.getBlockContent(block) }));
          }
        });
      ConvertClassicEditorService.removeNotice();
      wp.data
        .select("core/editor")
        .getBlocks()
        .forEach((block, blockIndex) => {
          let annotationService = new window.wordlift.AnnotationService(block);
          window.wordlift.store1.dispatch(annotationService.wordliftAnalyze());
        });
      window.wordlift.store1.dispatch(AnnotationService.analyseLocalEntities());
    };
  }
}

export default ConvertClassicEditorService;
