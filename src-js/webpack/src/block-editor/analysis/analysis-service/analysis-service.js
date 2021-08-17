/**
 * This file has interface for all analysis operations related to the block editor.
 *
 * @package block-editor/analysis
 * @author Naveen Muthusamy <naveen@wordlft.io>
 * @since 3.32.5
 */

export default class AnalysisService {

	embedAnalysis(editorOps, response) {
		throw new Error( "Method embedAnalysis not implemented" )
	}

	* toggleEntity({entity}) {
		throw new Error( "Method toggleEntity not implemented" )
	}

	* toggleLink({entity}) {
		throw new Error( "Method toggleLink not implemented" )
	}

	* toggleAnnotation({annotation}) {
		throw new Error( "Method toggleAnnotation not implemented" )
	}

	* handleAddEntityRequest({payload}) {
		throw new Error( "Method handleAddEntityRequest not implemented" )
	}


}
