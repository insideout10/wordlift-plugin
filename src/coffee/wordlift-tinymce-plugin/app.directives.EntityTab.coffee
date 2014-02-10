angular.module('wordlift.tinymce.plugin.directives.EntityTab', [
]).directive 'wordlift-entity-tab', ['$log', '$scope', '$http', ($log, $scope, $http) ->
	restrict: 'C',
	scope: true,
	controller: ['$log', '$scope', '$http', ($log, $scope, $http)->
		# Stored mapping between textannotation and entities
		# TODO Filter duplicated entities for presentation pourpose
    	$scope.entityMapping = {}
    	# Entities selected on page
    	$scope.onPageEntities = ()->
    		(val for key, val of $scope.entityMapping)
		# Riceive this event from HelloController
		$scope.$on 'DisambiguationWidget.entitySelected', (event, entity, textAnnotationId) ->
      		$log.debug "Received disambiguated entity for annotation with id ..."
    		$scope.entityMapping[textAnnotationId] = entity
    ],	
	template: """
		<div class="wl-entity-tab-wrapper">
		<h3>Entities</h3>
		<ul class="entities">
			<li ng-repeat="(index, entity) in onPageEntities()">
				<span>I'm entity</span>\n
				<input type="hidden" name="meta[{{index}}]['label']" value="A label" />\n
				<input type="hidden" name="meta[{{index}}]['description']" value="Description" />\n
				<input type="hidden" name="meta[{{index}}]['type']" value="Type" />\n
				<input type="hidden" name="meta[{{index}}]['thumbnail']" value="Thumbnail" />\n
			</li>
		</ul>
		</div>
	"""
	]
