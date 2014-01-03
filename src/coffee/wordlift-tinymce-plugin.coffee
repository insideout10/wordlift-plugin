tinymce.PluginManager.add 'wordlift', (editor, url) ->

    # Add a button that opens a window
    editor.addButton 'wordlift',
        text   : 'WordLift'
        icon   : false
        onclick: -> alert('Hello!')