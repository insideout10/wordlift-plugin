<?php

class WordLift {

	function analyze_text($id) {

		$logger = Logger::getLogger(__CLASS__);

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			$logger->debug('Caught an autosave. Exiting.');
      		return;
      	}

		$post_id = wp_is_post_revision( $id );
		if (false == $post_id) $post_id = $id;

		$post = get_post($post_id);
		if ('post' != $post->post_type) {
			$logger->debug('The post [id:'.$id.'] is of [type:'.$post->post_type.']: it won\'t be analyzed.');
			return null;
		}
		
		$job_id					= get_post_meta($post_id, POST_META_JOB_ID);

		$logger->debug("Post [post_id:$post_id][job_id:".var_export($job_id,true)."].");

		$post_content			= strip_tags($post->post_content);
		$job_request 			= new TextJobRequest($post_content, ON_COMPLETE_URL, ON_PROGRESS_URL, CHAIN_NAME );
		$enhancer_job_service 	= new EnhancerJobService();
		$job_response			= $enhancer_job_service->requestJob($job_request);

		$logger->debug("[job_response:".var_export($job_response,true)."]");

		update_post_meta($post_id, POST_META_JOB_ID, $job_response->id);

	}

}


?>