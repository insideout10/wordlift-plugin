/**
 * This file implements the v1 analysis.
 *
 * @package block-editor/analysis
 * @author Naveen Muthusamy <naveen@wordlft.io>
 * @since 3.32.5
 */

import AnalysisService from "./analysis-service";


export default class V2AnalysisService extends AnalysisService {

    embedAnalysis(editorOps, response) {
        // Do nothing, we dont want to annotate here.
    }

    * toggleEntity({entity}) {
        //TODO: we want to create or remove relation based on the toggle.
    }

    * toggleLink({entity}) {
        // Do nothing, we cant create links here.
    }

    * toggleAnnotation({annotation}) {
        // Do nothing, we wont have annotation.
    }

    * handleAddEntityRequest({payload}) {
        // TODO: implement this method and update the sidebar.
    }
}