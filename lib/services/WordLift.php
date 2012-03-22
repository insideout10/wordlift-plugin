<?php
require_once('JobService.php');
require_once('PostHelper.php');

class WordLift {

	private $logger;
	private $enhancer_job_service;
	private $job_service;
	private $post_helper;

	/*
	 * initializes the WordLift instance.
	 */
	function __construct() {
		global $post_helper, $enhancer_job_service, $job_service;

		$this->logger 			= Logger::getLogger(__CLASS__);
		$this->enhancer_job_service = $enhancer_job_service;
		$this->job_service 		= $job_service;
		$this->post_helper 		= $post_helper;
	}

	function analyze_text($post_id) {

		$post = $this->post_helper->get_post($post_id);

		if ('post' != $post->post_type) {
			// $this->logger->debug('The post [id:'.$id.'] is of [type:'.$post->post_type.']: it won\'t be analyzed.');
			return null;
		}
		
		$job 					= $this->job_service->get_job_by_post_id($post->ID);
		$this->logger->debug('A job [id:'.$job->id.'][state:'.$job->state.'][is_running:'.$job->is_running().'] exists for post [id:'.$post->ID.'].');

		if ($this->post_helper->is_autosave() && $job->is_running()) {
			$this->logger->debug('This is an auto-save and an existing job [id:'.$job->id.'][post_id:'.$job->post_id.'] is running. This request will be ignored.');
      		return;
		}

		$post_content			= strip_tags($post->post_content);
		$job_request 			= new TextJobRequest($post_content, WORDLIFT_20_URLS_ON_COMPLETE, WORDLIFT_20_URLS_ON_PROGRESS, WORDLIFT_20_CHAIN_NAME );
		$job_response			= $this->enhancer_job_service->requestJob($job_request);

		$job 					= $this->job_service->create($job_response->id, WORDLIFT_20_JOB_STATE_ANALYZING, $post->ID);
		$this->job_service->save($job);

		$this->logger->debug('A job [id:'.$job->id.'] has been created for post [id:'.$job->post_id.'].');

	}

}

$wordlift = new WordLift();

?>