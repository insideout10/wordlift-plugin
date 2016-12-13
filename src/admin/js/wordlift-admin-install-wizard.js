(function ($, settings) {

    // Wait for the document to be ready.
    $(function () {

        // Key validation: attach to all the input with a `wl-key` class.
        $('input.wl-key').each(function () {

            // Get a jQuery reference to the input.
            var $this = $(this);

            // Catch changes.
            $this.on('keydown', function () {

                // Get a jQuery reference to the object.
                var $this = $(this);

                // Clear a validation timeout.
                clearTimeout($this.data('timeout'));

                // Validate the key, after a delay, so that another key is pressed, this validation is cancelled.
                $this.data('timeout', setTimeout(function () {

                    // Post the validation request.
                    $.post(settings.ajaxUrl, {'action': settings.action, 'key': $this.val()},
                        function (data) {

                            // If the key is valid then set the process class.
                            if (data && data.valid) {
                                $this
                                    .removeClass('invalid')
                                    .addClass('valid');
                            } else {
                                $this
                                    .removeClass('valid')
                                    .addClass('invalid');
                            }

                        });

                }, 500));

            });

        });

        // Media upload: attach to all elements with `wl-add-logo` class.
        $('.wl-add-logo').each(function () {

            // Get a jQuery reference to the element.
            var $this = $(this);

            $this.on('click', function () {

                var uploader = wp.media({
                    title: settings.media.title,
                    button: settings.media.button,
                    multiple: false
                });

                uploader
                    .on('select', function () {

                        var attachment = uploader.state().get('selection').first().toJSON();

                        $('#logo img')
                            .attr('src', attachment.url)
                            .data('id', attachment.id);

                        $('#logo').show();
                        $('#addlogo').hide();
                    })
                    .open();

            });

        });

        // Logo: attach to the logo element.
        $('#logo').each(function () {

            // Get a jQuery reference to the element.
            var $this = $(this);

            // Handle clicks, by removing the logo.
            $this.on('click', function () {

                // Reset the logo data.
                $('> img')
                    .attr('src', '')
                    .data('id', '');

                // Hide the logo element.
                $this.hide();

                // Show again the add logo button.
                $('#addlogo').show();

            });

        });

    });

})(jQuery, _wlAdminInstallWizard);
