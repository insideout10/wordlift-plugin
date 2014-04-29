(function() {
    tinymce.create('tinymce.plugins.wl_chord', {
        /**
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init : function(ed, url) {
			ed.addButton('wl_chord',
				{
					title: 'wl_chord',
					cmd: 'wl_chord',
					image: 'http://wordlift3.localhost/wp-content/plugins/wordlift/images/wordlift-chord-black-20x20.png'
				}
			);
			
			ed.addCommand('wl_chord', function() {
			
				console.log("AAA");
				jQuery('#wordlift_chord_dialog').dialog({
					title: 'Wordlift Chord Graph',
					width: 400,
					height: 500
				});
				/*ed.windowManager.open({
					file: 'http://wordlift3.localhost/wp-content/plugins/wordlift/wordlift_shortcode_chord_tinymce_dialog.php',
					width: 500,
					height: 300,
					inline: true
				});*/
            });
        },
 
        /**
         * Creates control instances based in the incomming name. This method is normally not
         * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
         * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
         * method can be used to create those.
         *
         * @param {String} n Name of the control to create.
         * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
         * @return {tinymce.ui.Control} New control instance or null if no control was created.
         */
        createControl : function(n, cm) {
            return null;
        },
 
        /**
         * Returns information about the plugin as a name/value array.
         * The current keys are longname, author, authorurl, infourl and version.
         *
         * @return {Object} Name/value array containing information about the plugin.
         */
        getInfo : function() {
            return {
                longname : 'WordLift Chord Button',
                author : 'Piero Savastano @ InsideOut10',
                /*authorurl : 'http://wp.tutsplus.com/author/leepham',
                infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/example',*/
                version : "1.0"
            };
        }
    });
 
    // Register plugin
    tinymce.PluginManager.add( 'wl_chord', tinymce.plugins.wl_chord );
})();
