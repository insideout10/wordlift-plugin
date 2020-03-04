<?php
/**
 * This file defines the translation strings for Faq tinymce plugin
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @package Wordlift\FAQ
 */


$strings = 'tinyMCE.addI18n(
    {' . _WP_Editors::$mce_locale . '.wordliftFaqStrings:
        {
            addQuestionText: "' . esc_js( __( 'Add Question', 'wordlift' ) ) . '",
            addAnswerText: "' . esc_js( __( 'Add Answer', 'wordlift' ) ) . '",
            addQAText: "' . esc_js( __( 'Add Question / Answer', 'wordlift' ) ) . '",
        }
    }
)';
