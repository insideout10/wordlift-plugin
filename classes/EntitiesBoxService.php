<?php
require_once('JobService.php');

class EntitiesBoxService {
	
	private $job_service;
	private $logger;

	function __construct() {
		global $job_service;

		$this->logger 		= Logger::getLogger(__CLASS__);
		$this->job_service 	= $job_service;
	}

	function custom_box($post) {

		$job 				= $this->job_service->get_job_by_post_id($post->ID);

		echo 'An analysis job for this post is '.$job->state.'.<br/>';
	?>

		<style>
			.entity-item {
				background: white;
				border: 1px solid black;
				width: 140px;
				min-height: 50px;
				margin: 2px;
				border-radius: 0.2em;
			}

			.entity-caption-outer {
				position: absolute;
				bottom: 0;
				background: white;
				width: 100%;
				opacity: 0.7;
				-moz-opacity: 0.7;
				filter:alpha(opacity=7);
			}

			.entity-caption-inner {
				padding: 2px 4px;
				font-weight: bold;
			}
		</style>
		<script type="text/template" id="entities-template">
			<% _.each(entities, function(entity) { %>
				<div class="isotope-item entity-item <%= entity.type %>" data-reference="<%= entity.reference %>">
					<% if (entity.properties[0]['thumbnail']) { %>
						<div><img style="width: 100%;" alt="" onerror="jQuery(this).remove();" src="<%= entity.properties[0]['thumbnail'] %>" /></div>
					<% } %>
					<div class="entity-caption-outer">
						<div class="entity-caption-inner"><%= entity.text %></div>
					</div>
				</div>
			<% }); %>
		</script>

		[<a href="javascript:jQuery('#entities-container').isotope({ filter: '.Person' });">People</a>][<a href="javascript:jQuery('#entities-container').isotope({ filter: '.Place' });">Places</a>][<a href="javascript:jQuery('#entities-container').isotope({ filter: '.CreativeWork' });">Creative Works</a>][<a href="javascript:jQuery('#entities-container').isotope({ filter: '.Organization' });">Organizations</a>][<a href="javascript:jQuery('#entities-container').isotope({ filter: '.Other' });">Other</a>][<a href="javascript:jQuery('#entities-container').isotope({ filter: '' });">All</a>]
		<div id="entities-container" class="isotope" style="width: 100%;"></div>

	<?php

	}

	function create_custom_box() {
		add_meta_box( 
	        'wordlift_20_entities',
	        _x( 'Entities', 'Entities (Custom-Box Title)' ),
	        array( $this, 'custom_box'),
	        'post' 
	    );
	}

}

$entities_box_service = new EntitiesBoxService();

?>