angular.module('wordlift.tinymce.plugin.directives', [
	'wordlift.tinymce.plugin.controllers'
])
.directive 'wlMetaBoxSelectedEntity', ()->
	restrict: 'AE',
	scope:
		index: '='
		entity: '='
	template: """
		<span>{{entity.label}} (<small>{{entity.type}}</span>)\n
		<br /><small>{{entity.thumbnail}}</small>
		<input type="hidden" name="meta[{{index}}]['label']" value="{{entity.label}}" />\n
		<input type="hidden" name="meta[{{index}}]['description']" value="{{entity.description}}" />\n
		<input type="hidden" name="meta[{{index}}]['type']" value="{{entity.type}}" />\n
		<input type="hidden" name="meta[{{index}}]['thumbnail']" value="{{entity.thumbnail}}" />\n
	"""