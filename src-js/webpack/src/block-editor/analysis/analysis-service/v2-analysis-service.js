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
        super.embedAnalysis(editorOps, response);
    }

    * toggleEntity({entity}) {
        yield* super.toggleEntity({entity});
    }

    * toggleLink({entity}) {
        yield* super.toggleLink({entity});
    }

    * toggleAnnotation({annotation}) {
        yield* super.toggleAnnotation({annotation});
    }

    * handleAddEntityRequest({payload}) {
        yield* super.handleAddEntityRequest({payload});
    }
}