angular.module('wordlift.editpost.widget.services.EditorAdapter', [
  'wordlift.editpost.widget.services.EditorAdapter'
])
# An adapter to the page editor.
#
# @since 1.4.2
.service('EditorAdapter', ['$log', ($log)->

  # Create the service definition.
  service =

    # Get the editor with the specified id (by default 'content').
    #
    # @since 1.4.2
    #
    # @param string id The editor's id (by default 'content').
    # @return The editor instance.
    getEditor: (id = window['wlSettings']['default_editor_id'] ? 'content') ->
      tinyMCE.get( wp?.hooks?.applyFilters( 'wl_default_editor_id', id ) ? id )

    # Get the HTML code in the specified editor (by default 'content').
    #
    # @since 1.4.2
    #
    # @param string id The editor's id (by default 'content').
    # @return The editor's HTML content.
    getHTML: (id = window['wlSettings']['default_editor_id'] ? 'content') ->
      service.getEditor(id).getContent format: 'raw'

  # Finally return the service.
  service
])