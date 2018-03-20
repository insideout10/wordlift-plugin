angular.module('wordlift.editpost.widget.services.AnnotationParser', [])
# Parse annotations.
#
# @since 1.4.2
.service('AnnotationParser', ['$log', ($log)->

  # Define the service.
  service =

    # Parse the provided HTML code for annotations.
    #
    # @since 1.4.2
    #
    # @param string html The HTML code to parse.
    # @return array An array of annotations.
    parse: (html) ->

      # Prepare a traslator instance that will traslate Html and Text positions.
      traslator = Traslator.create html
      # Set the pattern to look for *itemid* attributes.
      pattern = /<(\w+)[^>]*\sitemid="([^"]+)"[^>]*>([^<]*)<\/\1>/gim

      # Get the matches and return them.
      (while match = pattern.exec html

        annotation =
          start: traslator.html2text match.index
          end: traslator.html2text (match.index + match[0].length)
          uri: match[2]
          label: match[3]

        annotation
      )

  # Finally return the service.
  service
])