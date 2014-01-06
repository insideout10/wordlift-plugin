angular.module('wordlift.tinymce.plugin.services', ['wordlift.tinymce.plugin.config'])
  .service('EditorService', ['AnnotationService', '$rootScope', (AnnotationService, $rootScope) ->

    $rootScope.$on 'DisambiguationWidget.entitySelected', (event, entity) ->
      console.log "Going to map entity #{entity['@id']} on textAnnotation #{entity['dc:relation']}"
      
      cssClasses = "textannotation #{entity['wordlift:cssClasses']} disambiguated"

      tinyMCE.get("content").dom.setAttrib(entity['dc:relation'], 'class', cssClasses);
      tinyMCE.get("content").dom.setAttrib(entity['dc:relation'], 'itemscope', 'itemscope');
      tinyMCE.get("content").dom.setAttrib(entity['dc:relation'], 'itemtype',  entity['wordlift:supportedTypes'].join(' '));
      tinyMCE.get("content").dom.setAttrib(entity['dc:relation'], 'itemprop', 'name');
      tinyMCE.get("content").dom.setAttrib(entity['dc:relation'], 'itemid', entity['enhancer:entity-reference']);

    $rootScope.$on 'AnnotationService.annotations', (event, annotations) ->
      console.log 'I received some annotations'

      currentHtmlContent = tinyMCE.get('content').getContent({format : 'raw'})

      for textAnnotation in annotations
        # get the selection prefix and suffix for the regexp.
        selPrefix = textAnnotation['enhancer:selection-prefix']['@value'].replace( '\(', '\\(' ).replace( '\)', '\\)' )
        selPrefix = '^' if '' is selPrefix
        selSuffix = textAnnotation['enhancer:selection-suffix']['@value'].replace( '\(', '\\(' ).replace( '\)', '\\)' )
        selSuffix = '$' if '' is selSuffix

        selText   = textAnnotation['enhancer:selected-text']['@value']

        # this is the old regular expression which is not accurate.
        # regexp = new RegExp( "(\\W|^)(#{selText})(\\W|$)(?![^<]*\">?)" )

        # the new regular expression, may not match everything.
        # TODO: enhance the matching.
        regexp = new RegExp( "(#{selPrefix})(#{selText})(#{selSuffix})(?![^<]*\">?)" )

        console.log regexp
        replace = "$1<span id=\"#{textAnnotation['@id']}\" class=\"textannotation\"
          typeof=\"http://fise.iks-project.eu/ontology/TextAnnotation\">$2</span>$3"

        currentHtmlContent = currentHtmlContent.replace( regexp, replace )

        isDirty = tinyMCE.get( "content").isDirty()
        tinyMCE.get( "content").setContent( currentHtmlContent )
        tinyMCE.get( "content").isNotDirty = 1 if not isDirty

      tinyMCE.get( "content").onClick.add (editor, e) ->
        $rootScope.$apply(
          console.log("Click within the editor on element with id #{e.target.id}")
          $rootScope.$broadcast 'EditorService.annotationClick', e.target.id
        )

    ping: (message)    -> console.log message
    analyze: (content) -> AnnotationService.analyze content

  ])
  .service('AnnotationService', ['$rootScope', '$http', ($rootScope, $http) ->
    
    currentAnalysis = {}
    supportedTypes = [
      'schema:Place'
      'schema:Event'
      'schema:CreativeWork'
      'schema:Product'
      'schema:Person'
      'schema:Organization'
    ]
    
    $rootScope.$on 'EditorService.annotationClick', (event, id) ->
      console.log "Ops!! Element with id #{id} was clicked!"
      findEntitiesForAnnotation(id)
    
    # Intersection
    intersection = (a, b) ->
      [a, b] = [b, a] if a.length > b.length
      value for value in a when value in b

    # Find all text annotation for the current analyzed text
    findAllAnnotations = () ->
      textAnnotations = currentAnalysis['@graph'].filter (item) ->
          'enhancer:TextAnnotation' in item['@type'] and item['enhancer:selection-prefix']?
      $rootScope.$broadcast 'AnnotationService.annotations', textAnnotations    
    
    # Find all Entities for a certain text annotation identified by 'annotationId'
    findEntitiesForAnnotation = (annotationId) ->
      console.log "Going to find entities for annotation with ID #{annotationId}"
        
      entityAnnotations = currentAnalysis['@graph'].filter (item) ->
          'enhancer:EntityAnnotation' in item['@type'] and item['dc:relation'] == annotationId
      # Enhance entity annotations ..
      entityAnnotations = entityAnnotations.map (item) ->
         
          if item['enhancer:entity-type']
            i = intersection(supportedTypes, item['enhancer:entity-type'])
            item['wordlift:supportedTypes'] = i.map (type) ->
              "http://schema.org/#{type.replace(/schema:/,'')}" 
            item['wordlift:cssClasses'] = i.map (type) ->
              "#{type.replace(/schema:/,'')}".toLowerCase()
            item['wordlift:cssClasses'] = item['wordlift:cssClasses'].join(' ')
          else
            item['wordlift:supportedTypes'] = []
            item['wordlift:cssClasses'] = ''

          item
      # Retrieve related entities ids
      # entityIds = entityAnnotations.map (entityAnnotation) ->
      #  entityAnnotation['enhancer:entity-reference']
      # Retrieve related entities 
      # entities = currentAnalysis['@graph'].filter (item) ->
      #  item['@id'] in entityIds 

      $rootScope.$broadcast 'AnnotationService.entityAnnotations', entityAnnotations   
    
    # Call redlink api trough Wp Ajax bridge in order to perform the semantic analysis
    analyze: (content) ->
      $http
        method: 'POST'
        url: ajaxurl
        params:
          action: 'wordlift_analyze'
        data: content
      .success (data, status, headers, config) -> 
        # Set type
        currentAnalysis = data
        findAllAnnotations()
      true
  ])
  .service('DataSetService', ['$rootScope', '$http', ($rootScope, $http) ->
  