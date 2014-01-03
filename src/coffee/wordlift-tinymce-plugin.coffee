tinymce.PluginManager.add 'wordlift', (editor, url) ->

    # Add a button that opens a window
    editor.addButton 'wordlift',
        text   : 'WordLift'
        icon   : false
        onclick: -> 
            content = tinyMCE.activeEditor.getContent({format : 'raw'})
            alert(content)