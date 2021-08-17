/**
 * This file has interface for all analysis operations related to the block editor.
 *
 * @package block-editor/analysis
 * @author Naveen Muthusamy <naveen@wordlft.io>
 * @since 3.32.5
 */

export default class AnalysisService {

	/**
	 * Function to be called to annotate the text annotations.
	 *
	 * @param editorOps
	 * @param response
	 */
	embedAnalysis(editorOps, response) {
		throw new Error( "Method embedAnalysis not implemented" )
	}

	/**
	 * Action fired when the entity is selected by the user.
	 *
	 * @param entity
	 * @returns {Generator<*, void, *>}
	 */
	* toggleEntity({entity}) {
		throw new Error( "Method toggleEntity not implemented" )
	}

	/**
	 * Action fired when the link is turned on in the entity sidebar.
	 *
	 * @param entity
	 * @returns {Generator<*, void, *>}
	 */
	* toggleLink({entity}) {
		throw new Error( "Method toggleLink not implemented" )
	}

	/**
	 * Handle `ANNOTATION` actions.
	 *
	 * When the `ANNOTATION` action is fired, the `selected` css class will be added
	 * to the selected annotation and removed from the others.
	 *
	 * The annotation id should match the element id.
	 *
	 * @since 3.23.0
	 * @param {string|undefined} annotationId The annotation id.
	 */
	* toggleAnnotation({annotation}) {
		throw new Error( "Method toggleAnnotation not implemented" )
	}


	/**
	 * Handles the request to add an entity.
	 *
	 * First we toggle the wordlift/annotation in Block Editor to create the annotation.
	 */
	* handleAddEntityRequest({payload}) {
		throw new Error( "Method handleAddEntityRequest not implemented" )
	}


}
