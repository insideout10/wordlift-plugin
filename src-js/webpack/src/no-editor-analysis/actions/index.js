/**
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.32.6
 * Actions fired on the no editor analysis.
 */


/**
 * Internal dependencies.
 */
import {ANALYSIS_COMPLETE, ANALYSIS_RUNNING, NO_EDITOR_SYNC_FORM_DATA} from "./types";

export const syncFormData = () => ({ type: NO_EDITOR_SYNC_FORM_DATA })

export const analysisStateChanged = (isRunning) => {
    if ( isRunning ) {
        return { type: ANALYSIS_RUNNING}
    }
    return { type: ANALYSIS_COMPLETE }
}