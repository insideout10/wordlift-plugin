import {ANNOTATION_CHANGED, SELECTION_CHANGED} from "../common/constants";
import {isAnnotationElement} from "../common/helpers";

/**
 * Just boiler plate code to check if the approach is possible.
 *
 */
const tinymce = global["tinymce"];

tinymce.init({  selector: 'textarea',
    setup: function(editor) {
        editor.on('Change', function(e) {
            console.log(e)
            console.log('The Editor has initialized.');
        });
    }
})