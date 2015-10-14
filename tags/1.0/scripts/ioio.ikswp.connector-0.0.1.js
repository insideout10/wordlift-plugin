(function($) {

	$.ioio.ikswp.connector = {};

	$.ioio.ikswp.connector.lift = function() {

		// ceate the dialog
		var dialog = $(
				'<div id="dialog"><div id="bar">Select the entities you\'d like to save in the body of your post, then hit Save!<br/><button id="save-button" type="button">Save</button></div><div id="content"></div><div id="results"></div></div>')
				.appendTo(document.body).dialog({
					height : $(window).height() - 100,
					width : $(window).width() - 100,
					position : [ 50, 50 ],
					modal : true,
					autoOpen : false,
					title: 'IKS for WordPress by InsideOut10'
				});

		var dialogContent = $('#dialog #content').html(
				tinyMCE.get('content').getContent({
					format : 'raw'
				}));

		dialog.dialog('open');

		// remove any existing entities
		$('#content div.entity').remove();
		$("#results").empty();
		var c = $("#dialog #content").text()
				.replace(new RegExp("\\n", "g"), '');
		c = c.replace(new RegExp("\\t", "g"), ' ');
		c = c.replace(new RegExp('"', 'g'), '');

		$.ioio.ikswp.connector.analyze(c);

		$("#save-button").button().click(function() {
			$('div.entity.ui-selected').each(function() {
				$(this).removeClass('ui-selected ui-selectee');
				dialogContent.append($(this));
			});
			tinyMCE.get('content').setContent(dialogContent.html());

			dialog.dialog('close');

			//
			dialog.empty();
		});

	}

	$.ioio.ikswp.connector.analyze = function(content) {

		var loading = $(
				'<div><div style="width: 90%; height: 90%; text-align: center; padding: 8px;"><img width="48" height="48" src="../wp-content/plugins/wordlift/images/spinner-transparent-32x32.gif" /><br/><br/>Analyzing text...</div></div>')
				.dialog({
					modal : true,
					title : 'Loading...',
					resizable : false,
					draggable : false,
					dialogClass : 'alert',
					position : 'center',
					autoOpen : false
				});

		var stanbol = $.ioio.ikswp.stanbol($('#results'));

		stanbol.bind('loading', function() {
			console.log('[$.ioio.ikswp.connector.analyze] stanbol.loading');
			loading.dialog('open');
		});
		stanbol.bind('loaded', function() {
			$("#results").selectable({
				filter : '.entity'
			});
			loading.dialog('close');
		});

		stanbol.analyze(content);

	}

})(jQuery);
