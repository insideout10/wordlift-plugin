(function() {
  window.wlEnabledBlocks = window.wlEnabledBlocks || [
    "wordlift/products-navigator",
    "wordlift/navigator",
    "wordlift/chord",
    "wordlift/geomap",
    "wordlift/timeline",
    "wordlift/cloud",
    "wordlift/vocabulary",
    "wordlift/faceted-search"
  ];
  tinymce.create("tinymce.plugins.wl_shortcodes", {
    // Custom function for the Chord
    chordClick: function() {
      const chord_shortcode_text = "[wl_chord]";
      top.tinymce.activeEditor.execCommand(
        "mceInsertContent",
        false,
        chord_shortcode_text
      );
    },

    // Custom function for the Timeline
    timelineClick: function() {
      const timeline_shortcode_text = "[wl_timeline]";
      top.tinymce.activeEditor.execCommand(
        "mceInsertContent",
        false,
        timeline_shortcode_text
      );
    },

    geomapClick: function() {
      const geomap_shortcode_text = "[wl_geomap]";
      top.tinymce.activeEditor.execCommand(
        "mceInsertContent",
        false,
        geomap_shortcode_text
      );
    },

    navigatorClick: function() {
      const navigator_shortcode_text = "[wl_navigator]";
      top.tinymce.activeEditor.execCommand(
        "mceInsertContent",
        false,
        navigator_shortcode_text
      );
    },

    productsNavigatorClick: function() {
      const products_navigator_shortcode_text = "[wl_products_navigator]";
      top.tinymce.activeEditor.execCommand(
        "mceInsertContent",
        false,
        products_navigator_shortcode_text
      );
    },

    facetedSearchClick: function() {
      const faceted_search_shortcode_text = "[wl_faceted_search]";
      top.tinymce.activeEditor.execCommand(
        "mceInsertContent",
        false,
        faceted_search_shortcode_text
      );
    },

    cloudClick: function() {
      const cloud_shortcode_text = "[wl_cloud]";
      top.tinymce.activeEditor.execCommand(
        "mceInsertContent",
        false,
        cloud_shortcode_text
      );
    },

    glossaryClick: function() {
      const glossary_shortcode_text = "[wl_vocabulary]";
      top.tinymce.activeEditor.execCommand(
        "mceInsertContent",
        false,
        glossary_shortcode_text
      );
    },

    /**
     * @param {tinymce.Editor} ed Editor instance that the plugin is
     *     initialized in.
     * @param {string} url Absolute URL to where the plugin is located.
     */
    init: function(ed, url) {
      // Store url inside plugin (needed for the createControl()
      // function.
      tinymce.plugins.wl_shortcodes.pluginUrl = url;

      // Check tinyMCE version. If the version is 4 the menu will be
      // built here, otherwise in the createControl() function.
      if (tinymce.majorVersion == 4) {
        const menu4 = [];
        window.wlEnabledBlocks.forEach(item => {
          switch (item) {
            case "wordlift/products-navigator":
              menu4.push({
                text: "Products Navigator",
                onclick: this.productsNavigatorClick
              });
              break;
            case "wordlift/navigator":
              menu4.push({
                text: "Navigator",
                onclick: this.navigatorClick
              });
              break;
            case "wordlift/chord":
              menu4.push({
                text: "Chord",
                onclick: this.chordClick
              });
              break;
            case "wordlift/geomap":
              menu4.push({
                text: "GeoMap",
                onclick: this.geomapClick
              });
              break;
            case "wordlift/timeline":
              menu4.push({
                text: "Timeline",
                onclick: this.timelineClick
              });
              break;
            case "wordlift/cloud":
              menu4.push({
                text: "Entity Cloud",
                onclick: this.cloudClick
              });
              break;
            case "wordlift/vocabulary":
              menu4.push({
                text: "Vocabulary Widget",
                onclick: this.glossaryClick
              });
              break;
            case "wordlift/faceted-search":
              menu4.push({
                text: "Faceted Search",
                onclick: this.facetedSearchClick
              });
              break;
            default:
          }
        });

        const btn = ed.addButton("wl_shortcodes_menu", {
          type: "menubutton",
          title: "Widgets",
          text: "Widgets",
          classes: "wl-button",
          // Must define onclick to avoid error
          onclick: function() {
            /* OPEN YOURSELF ?? */
          },
          menu: menu4
        });
      }
    },

    /**
     * Creates control instances based in the incomming name. This
     * method is normally not needed since the addButton method of the
     * tinymce.Editor class is a more easy way of adding buttons but
     * you sometimes need to create more complex controls like
     * listboxes, split buttons etc then this method can be used to
     * create those.
     *
     * @param {String} n Name of the control to create.
     * @param {tinymce.ControlManager} cm Control manager to use
     *     inorder to create new control.
     * @return {tinymce.ui.Control} New control instance or null if no
     *     control was created.
     */
    createControl: function(n, cm) {
      // Taking a reference to the plugin object.
      pluginRef = this;

      // Check tinyMCE version and build menu.
      if (tinymce.majorVersion == 3 && n == "wl_shortcodes_menu") {
        //const c = cm.createSplitButton('wl_shortcodes_menu',
        // {	//split button not working properly
        const c = cm.createMenuButton("wl_shortcodes_menu", {
          title: "WordLift Widgets",
          image:
            tinymce.plugins.wl_shortcodes.pluginUrl +
            "/../images/svg/wl-logo-icon.svg?ver=3.33.8"
          // If SplitButton, must define onclick to avoid error
          //onclick: function(){ /* OPEN YOURSELF ?? */  }
        });

        c.onRenderMenu.add(function(c, m) {
          window.wlEnabledBlocks.forEach(item => {
            switch (item) {
              case "wordlift/products-navigator":
                m.add({
                  title: "Products Navigator",
                  onclick: pluginRef.productsNavigatorClick
                });
                break;
              case "wordlift/navigator":
                m.add({
                  title: "Navigator",
                  onclick: pluginRef.navigatorClick
                });
                break;
              case "wordlift/chord":
                m.add({
                  title: "Chord",
                  onclick: pluginRef.chordClick
                });
                break;
              case "wordlift/geomap":
                m.add({
                  title: "GeoMap",
                  onclick: pluginRef.geomapClick
                });
                break;
              case "wordlift/timeline":
                m.add({
                  title: "Timeline",
                  onclick: pluginRef.timelineClick
                });
                break;
              case "wordlift/cloud":
                m.add({
                  title: "Entities Cloud",
                  onclick: pluginRef.cloudClick
                });
                break;
              case "wordlift/vocabulary":
                m.add({
                  title: "Entities Glossary",
                  onclick: pluginRef.glossaryClick
                });
                break;
              case "wordlift/faceted-search":
                m.add({
                  title: "Faceted Search",
                  onclick: pluginRef.facetedSearchClick
                });
                break;
              default:
            }
          });
        });

        // Return the new menubutton instance
        return c;
      }
      return null;
    },

    /**
     * Returns information about the plugin as a name/value array.
     * The current keys are longname, author, authorurl, infourl and
     * version.
     *
     * @return {Object} Name/value array containing information about
     *     the plugin.
     */
    getInfo: function() {
      return {
        longname: "WordLift Shortcodes",
        author: "WordLift",
        version: "3.22.0"
      };
    }
  });

  // Register plugin
  tinymce.PluginManager.add("wl_shortcodes", tinymce.plugins.wl_shortcodes);
})();
