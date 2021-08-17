/**
 * This file provides the instance of analysis service based on the feature enabled.
 *
 * @package block-editor/analysis
 * @author Naveen Muthusamy <naveen@wordlft.io>
 * @since 3.32.5
 */

import V1AnalysisService from "./analysis-service/v1-analysis-service";
import V2AnalysisService from "./analysis-service/v2-analysis-service";

export default class AnalysisServiceFactory {

	/**
	 * Return the analysis service based on the feature.
	 *
	 * @return AnalysisService
	 */
	static  getAnalysisService() {
		return new V1AnalysisService();
	}


}
