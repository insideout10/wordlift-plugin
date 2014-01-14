angular.module('wordlift.tinymce.plugin.config', [])
	.constant 'Configuration', 
		supportedTypes: [
			'schema:Place'
			'schema:Event'
			'schema:CreativeWork'
			'schema:Product'
			'schema:Person'
			'schema:Organization'
		]
		entityLabels:
			'entityLabel': 'enhancer:entity-label'
			'entityType': 'enhancer:entity-type'
			'entityReference': 'enhancer:entity-reference'
			'textAnnotation': 'enhancer:TextAnnotation'
			'entityAnnotation': 'enhancer:EntityAnnotation'    		
			'selectionPrefix': 'enhancer:selection-prefix'
			'selectionSuffix': 'enhancer:selection-suffix'
			'selectedText': 'enhancer:selected-text'
			'confidence': 'enhancer:confidence'
			'relation':	'dc:relation'