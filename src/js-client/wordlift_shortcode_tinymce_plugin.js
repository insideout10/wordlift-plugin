
(function() {
    tinymce.create('tinymce.plugins.wl_shortcodes', {
    	
    	// Custom function for the Chord
    	chordClick : function(){
    		jQuery('#wordlift_chord_dialog').dialog({
				title: 'Wordlift Chord Graph',
				width: 400,
				height: 500
			});
    	},
    	
    	// Custom function for the Timeline
    	timelineClick : function(){
	    	var timeline_shortcode_text = '[wl-timeline]';
			top.tinymce.activeEditor.execCommand('mceInsertContent', false, timeline_shortcode_text);
    	},
    	
    	// Custom function for the Related Posts
    	relatedPostsClick : function(){
	    	var timeline_shortcode_text = '[wl-related-posts]';
			top.tinymce.activeEditor.execCommand('mceInsertContent', false, timeline_shortcode_text);
    	},
    	
        /**
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
		 init : function(ed, url) {
		 	
		 	// Store url inside plugin (needed for the createControl() function.
		 	tinymce.plugins.wl_shortcodes.pluginUrl = url;
		 	    	
        	// Check tinyMCE version. If the version is 4 the menu will be built here,
        	// otherwise in the createControl() function.
        	if( tinymce.majorVersion==4 ) {
        		var btn = ed.addButton('wl_shortcodes_menu', {
			        type: 'menubutton',
			        title: 'WordLift graphs',
			        text: 'WordLift graphs',
			        image: url + '/../images/wordlift-logo-20x20.png',
			        // Must define onclick to avoid error
			        onclick: function(){ /* OPEN YOURSELF ?? */ },
			        menu: [
			            {
			            	text: 'Chord',
			            	onclick: this.chordClick
			            },
			            {
			            	text: 'Timeline',
			            	onclick: this.timelineClick
			            },
			            {
			            	text: 'Related Posts',
			            	onclick: this.relatedPostsClick
			            }
			        ]
			    });
        	}
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
			
			// Taking a reference to the plugin object.
            pluginRef = this;
			
			// Check tinyMCE version and build menu.
            if( tinymce.majorVersion==3 && n=='wl_shortcodes_menu'){

				//var c = cm.createSplitButton('wl_shortcodes_menu', {	//split button not working properly
                var c = cm.createMenuButton('wl_shortcodes_menu', {
                    title : 'WordLift graphs',
                    image : tinymce.plugins.wl_shortcodes.pluginUrl + '/../images/wordlift-logo-20x20.png',
                	// If SplitButton, must define onclick to avoid error
                	//onclick: function(){ /* OPEN YOURSELF ?? */  }
                });

                c.onRenderMenu.add(function(c, m) {
                	
                    //m.add({title : 'Wordlift widgets', 'class' : 'mceMenuItemTitle'}).setDisabled(1);

                    m.add({
                    	title: 'Chord',
                    	onclick: pluginRef.chordClick
                    });

                    m.add({
                    	title: 'Timeline',
                    	onclick: pluginRef.timelineClick
                    });
                    
                    m.add({
                    	title: 'Related Posts',
                    	onclick: pluginRef.relatedPostsClick
                    });
            	});

              // Return the new menubutton instance
              return c;
            }
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
                longname : 'WordLift Shortcodes',
                author : 'InsideOut10',
                /*authorurl : 'http://...',
                infourl : 'http://...',*/
                version : "1.0"
            };
        }
    });
 
    // Register plugin
    tinymce.PluginManager.add( 'wl_shortcodes', tinymce.plugins.wl_shortcodes );
})();
