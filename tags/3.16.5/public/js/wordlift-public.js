(function($, settings) {
  'use strict';

  window.addEventListener('load', function() {
    // Check if the JSON-LD is disabled, i.e. if there's a `jsonld_enabled`
    // setting explicitly defined with a value different from '1'.
    if (
      'undefined' !== typeof settings['jsonld_enabled'] &&
      '1' !== settings['jsonld_enabled']
    ) {
      return;
    }

    // Check that we have a post id or it's homepage, otherwise exit.
    if (
      typeof settings.postId === 'undefined' &&
      typeof settings.isHome === 'undefined'
    ) {
      return;
    }

    const requestData = {
      action: 'wl_jsonld'
    };

    // Check that we have a post id, and add it to the requestData.
    if (typeof settings.postId !== 'undefined') {
      requestData.id = settings.postId;
    }

    // Check that we have param that indicates we are on homepage, and add it
    // to the requestData.
    if (typeof settings.isHome !== 'undefined') {
      requestData.homepage = true;
    }

    // Request the JSON-LD data.
    $.get(settings.ajaxUrl, requestData, function(data) {
      // Append the data in the page head.
      $('head').append(
        '<script type="application/ld+json">' +
          JSON.stringify(data) +
          '</s' +
          'cript>'
      );
    });
  });
})(jQuery, wlSettings);
