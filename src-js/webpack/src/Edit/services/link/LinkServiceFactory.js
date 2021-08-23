/**
 * @since 3.32.6
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This class provides link service based on the analysis enabled.
 */

import LinkService from "./LinkService";
import NoAnnotationLinkService from "./NoAnnotationLinkService";

export default  class LinkServiceFactory {

    /**
     * @return LinkServiceInterface
     * @param linkByDefault
     */
    static  getInstance( ) {
        if ( this.isNoEditorAnalysisActive() ) {
            return new NoAnnotationLinkService();
        }
        return new LinkService();

    }

    static isNoEditorAnalysisActive() {
        return wlSettings !== undefined
            && wlSettings.analysis !== undefined
            && wlSettings.analysis.isEditorPresent !== undefined
            && wlSettings.analysis.isEditorPresent === false
    }

}