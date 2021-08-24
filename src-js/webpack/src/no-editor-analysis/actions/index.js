import {ANALYSIS_COMPLETE, ANALYSIS_RUNNING, NO_EDITOR_SYNC_FORM_DATA} from "./types";

export const syncFormData = () => ({ type: NO_EDITOR_SYNC_FORM_DATA })

export const analysisStateChanged = (isRunning) => {
    if ( isRunning ) {
        return { type: ANALYSIS_RUNNING}
    }
    return { type: ANALYSIS_COMPLETE }
}