/**
 * This file implements the v1 analysis.
 *
 * @package block-editor/analysis
 * @author Naveen Muthusamy <naveen@wordlft.io>
 * @since 3.32.5
 */

import AnalysisService from "./analysis-service";


export default class V1AnalysisService extends AnalysisService {

	embedAnalysis(editorOps, response) {
		// Bail out if the response doesn't contain results.
		if ("undefined" === typeof response || "undefined" === typeof response.annotations) {
			return;
		}

		const annotations = Object.values( response.annotations ).sort(
			function (a1, a2) {
				if (a1.end > a2.end) {
					return -1;
				}
				if (a1.end < a2.end) {
					return 1;
				}

				return 0;
			}
		);

		annotations.forEach(
			annotation =>
			editorOps.insertAnnotation( annotation.annotationId, annotation.start, annotation.end )
		);

		editorOps.applyChanges();
	}


	* toggleEntity({entity}) {
		yield * super.toggleEntity( {entity} );
	}

	* toggleLink({entity}) {
		yield * super.toggleLink( {entity} );
	}

	* toggleAnnotation({annotation}) {
		yield * super.toggleAnnotation( {annotation} );
	}

	* handleAddEntityRequest({payload}) {
		yield * super.handleAddEntityRequest( {payload} );
	}
}
