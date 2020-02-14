( function( wp ) {
    const MyCustomButton = function( props ) {
        return wp.element.createElement(
            wp.editor.RichTextToolbarButton, {
                icon: false,
                title: 'Add Question',
                onClick: function() {
                    console.log( 'toggle format' );
                },
            }
        );
    }
    wp.richText.registerFormatType(
        'my-custom-format/sample-output', {
            title: 'Sample output',
            tagName: 'samp',
            className: null,
            edit: MyCustomButton,
        }
    );
} )( window.wp );