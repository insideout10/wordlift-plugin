<?php
require_once('JobModel.php');

class JobService {
	
	private $logger;

	function __construct() {
		$this->logger 		= Logger::getLogger(__CLASS__);
	}

	function create($job_id, $job_state, $post_id) {
		return new JobModel(
			$job_id,
			$job_state,
			$post_id);
	}

	function save(&$job) {
		delete_post_meta($job->post_id, WORDLIFT_20_POST_META_JOB_ID);
		delete_post_meta($job->post_id, WORDLIFT_20_POST_META_JOB_STATE);
		add_post_meta($job->post_id, WORDLIFT_20_POST_META_JOB_ID, 		$job->id,		true);
		add_post_meta($job->post_id, WORDLIFT_20_POST_META_JOB_STATE, 	$job->state, 	true);
	}

	/*
	 * gets the job for the post.
	 */
	function get_job_by_post_id(&$post_id) {
		$custom_fields		= get_post_custom($post_id);
		
		$job = new JobModel(
			$custom_fields[WORDLIFT_20_POST_META_JOB_ID][0],
			$custom_fields[WORDLIFT_20_POST_META_JOB_STATE][0],
			$post_id);

		$this->logger->debug('A job has been retrieved [id:'.$job->id.'][state:'.$job->state.'].');

		return $job;
	}

	function get_job_by_id(&$job_id) {
		$this->logger->debug('Getting a job with id ['.$job_id.']');

		$posts = get_posts(array(
			'numberposts' 	=> 1,
			'post_status' 	=> array('publish','pending','draft','auto-draft','future','private','inherit'),
			'meta_key'    	=> WORDLIFT_20_POST_META_JOB_ID,
			'meta_value'  	=> $job_id
		));

		$post_id = $posts[0]->ID;

		return $this->get_job_by_post_id($post_id);
	}

}

$job_service = new JobService();

?>