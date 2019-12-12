<div id="container" style="width: 70%;">
</div>
<style>

    .wl-mappings-heading-text {
        font-size: 23px;
        font-weight: 400;
    }
    .wl-table {
        width: 70%;
    }
    .wl-table td input[type=checkbox], th input[type=checkbox]{
        vertical-align: inherit;
    }

    .wl-spaced-table td {
        padding-right: 150px;
    }
    
    .wl-check-column {
        vertical-align: inherit;
        width: 2.2em;
    }
    .wl-table td input[type=checkbox]{
        margin-left: 8px;
    }
    .wl-mappings-add-new:hover {
        background-color: #0073aa !important;
        color: #f7f7f7 !important;
    }
    .wl-postbox {
        position: relative;
        min-width: 255px;
        border: 1px solid #e5e5e5;
        box-shadow: 0 1px 1px rgba(0,0,0,.04);
        background: #fff;
        width: 90% !important;
    }
    .wl-bg-light {
        background-color: #fbfbfc !important;
    }
    .wl-input-class
    {
        height: 40px !important;
    }
    .wl-postbox {
        position: relative;
        min-width: 255px;
        border: 1px solid #e5e5e5;
        box-shadow: 0 1px 1px rgba(0,0,0,.04);
        background: #fff;
    }
    .wl-description {
        font-size: 13px;
        font-style: italic;
    }
    .wl-form-select {
        padding-top: 3px;
        padding-right: 5px;
        padding-bottom: 3px;
        padding-left: 0px !important;
        width: 150px;
    }
    .wl-form-control {
        width: 100%;
    }

    .wl-container {
        display: flex;
        justify-content: flex-start;
    }
    .wl-container-80 {
        width: 80%;
    }

    .wl-container-full {
        width: 100%;
    }
    
    .wl-col {
        padding: 1em;
    }
    .wl-align-right {
        margin-left: auto;
    }

    .wl-container-30 {
        width: 30%;
    }
    .wl-container-70 {
        width: 70%;
    }
    .wl-text-right {
        text-align: right;
    }
    .wl-remove-button {
        height: 33px;
        width: 33px;
        border-radius: 50%;
        visibility:hidden;
        vertical-align: inherit;
    }
    .wl-remove-button:hover {
        background-color: rgba(184,0,0,1);
        color: #fff;
    }
    .wl-rule-container:hover .wl-remove-button {
        visibility: visible;
    }


    .hide {
        display: none;
    }

    </style>
<?php
wp_enqueue_script( 'wl-sync-mappings-script' );
?>