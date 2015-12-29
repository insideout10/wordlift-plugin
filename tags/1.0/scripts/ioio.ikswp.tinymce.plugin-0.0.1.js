// Creates a new plugin class and a custom listbox
console.log( 'tinymce v'+tinymce.majorVersion+'.'+tinymce.minorVersion);

tinymce
		.create(
				'tinymce.plugins.io.SemanticLift',
				{
					SemanticLift : function(ed, url) {

						// adding RDFa support to SPAN tag to avoid tinymce
						// remove our attrs
						tinyMCE.activeEditor.settings.cleanup = false;
						tinyMCE.activeEditor.settings.extended_valid_elements = 'div[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick'
								+ '|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove'
								+ '|onmouseout|onmouseover|onmouseup|style|title'
								+ '|itemscope|itemprop|itemtype'
								+ '|typeof|property],'
								+ 'a[rel|rev|charset|hreflang|tabindex|accesskey|type'
								+ '|name|href|target|title|class|onfocus|onblur|itemprop],'
								+ 'span[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown'
								+ '|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover'
								+ '|onmouseup|style|title'
								+ '|itemscope|itemprop|itemtype'
								+ '|typeof|property|value]';
						
						// adding the button
						ed
								.addButton(
										'io_semantic_lift_button',
										{
											title : 'WordLift: Microdata Tagging of People and Organization',
											image : '../wp-content/plugins/wordlift/images/kubrick.jpg',
											// icons : false,
											onclick : function() {
												jQuery.ioio.ikswp.connector
														.lift();
											}
										});
					}
				});

// Register plugin with a short name
tinymce.PluginManager.add('semanticLift', tinymce.plugins.io.SemanticLift);
console.debug('Added the semanticLift plug-in to tinyMCE.');
