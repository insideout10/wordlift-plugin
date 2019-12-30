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


    .hide {
        display: none;
    }

    </style>
    <script>
        function show_second_mockup() {
            document.getElementById("second_mockup").style.display = "block";
            document.getElementById("first_mockup").style.display = "none";
        }
        function show_first_mockup() {
            document.getElementById("second_mockup").style.display = "none";
            document.getElementById("first_mockup").style.display = "block";
        }
    </script>

<div>
<!-- first mock up template -->
    <div id="first_mockup" class="hide">
        <h1 class="wp-heading-inline wl-mappings-heading-text">
            Mappings
        <button class="button wl-mappings-add-new" onclick="show_second_mockup()">
            Add New
        </button>
        </h1>
        <table class="wp-list-table widefat striped wl-table">
            <thead>
                <tr>
                    <th class="wl-check-column">
                        <input type="checkbox">
                    </th>
                    <th>
                        <a class="row-title">Title</a>
                    </th>
                </tr>
            </thead>
            <tr>
                <td class="wl-check-column">
                    <input type="checkbox">
                </td>
                <td>
                    <a class="row-title">
                        My custom post type
                    </a>
                    <div class="row-actions">
                        <span class="edit">
                            <a>Edit</a>
                            | 
                        </span>
                        <span>
                            <a title="Duplicate this item">Duplicate</a> |
                        </span>
                        <span class="trash">
                            <a>Trash</a>
                        </span>
                    </div>
                </td>
            </tr>

            <tr>
                <td class="wl-check-column">
                    <input type="checkbox">
                </td>
                <td>
                    <a class="row-title"> Another custom post type </a>
                    <div class="row-actions">
                        <span class="edit">
                            <a>Edit</a>
                            | 
                        </span>
                        <span>
                            <a title="Duplicate this item">Duplicate</a> |
                        </span>
                        <span class="trash">
                            <a>Trash</a>
                        </span>
                    </div>
                </td>
            </tr>
            <tfoot>
                <tr>
                    <th class="wl-check-column">
                        <input type="checkbox">
                    </th>
                    <th>
                        <a class="row-title">Title</a>
                    </th>
                </tr>
            </tfoot>

        </table>
        <div class="tablenav bottom">
                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-bottom" class="screen-reader-text">Select bulk action</label><select name="action2" id="bulk-action-selector-bottom">
                    <option value="-1">Bulk Actions</option>
                    <option value="acfduplicate">Duplicate</option>
                    <option value="trash">Move to Trash</option>
                    </select>
                    <input type="submit" id="doaction2" class="button action" value="Apply">
                </div>
            </div>
        </div>
    </div>
<!-- first mockup template end -->




<!-- second mock up template -->
    <div id="second_mockup" class="wl-container-70">
        <br/>
        <div class="text-left">
            <a onclick="show_first_mockup()" href="#">Go Back</a>
            <h1 class="wp-heading-inline wl-mappings-heading-text">
                Edit Mapping
            </h1>
        </div>
        <input type="text" class="wl-form-control wl-input-class"
         size="30" value="My Custom Post Type" id="title" spellcheck="true" autocomplete="off">
        <br/><br/>
        <table class="wp-list-table widefat striped wl-table wl-container-full">
            <thead>
                <tr>
                    <td colspan="0">
                       <b>Rules</b> 
                    </td>
                    <td colspan="2">
                    </td>
                </tr>
            </thead>
           <tr>
                <td class="wl-bg-light wl-description">
                    Here we show the help text
                </td>
                <td>
                    <div>
                        <b>Use the mapping if</b>
                    <div class="wl-container">
                        <div class="wl-col">
                            <select class="  wl-form-select">
                                <option value="-1">Post type</option>
                            </select>
                        </div>
                        <div class="wl-col">
                            <select class="  wl-form-select">
                                <option value="-1">is equal to</option>
                            </select>
                        </div>
                        <div class="wl-col">
                            <select class="  wl-form-select">
                                <option value="-1">Custom Post</option>
                            </select>
                        </div>
                        <div class="wl-col">
                            <button class="button action"> And </button>
                        </div>
                    </div>
                    <div class="wl-container">
                        <div class="wl-col">
                            <select class="  wl-form-select">
                                <option value="-1">Post taxonomy</option>
                            </select>
                        </div>
                        <div class="wl-col">
                            <select class="  wl-form-select">
                                <option value="-1">is equal to</option>
                            </select>
                        </div>
                        <div class="wl-col">
                            <select  class="  wl-form-select">
                                <option value="-1">My Term</option>
                            </select>
                        </div>
                        <div class="wl-col">
                            <button class="button action"> And </button>
                        </div>
                    </div>
                    <div class="wl-container">
                        <div class="wl-col">
                            <b>Or</b>
                        </div>
                    </div>
                    <div class="wl-container">
                        <div class="wl-col">
                            <button class="button action"> Add Rule Group </button>
                        </div>
                    </div>
                </td>
                <td>
                </td>
            </tr>
        </table>
        <br/>
        <table class="wp-list-table widefat striped wl-table wl-container-full">
            <thead>
                <tr>
                    <th class="wl-check-column">
                        <input type="checkbox"> 
                    </th>
                    <th style="width: 30%;">
                        <b>Property</b>
                    </th>
                    <th>
                        <b>Field</b>
                    </th>
                </tr>
            </thead>
            <tr>
                <td class="wl-check-column">
                    <input type="checkbox">
                </td>
                <td>
                    <div class="wl-container">
                            <div class="wl-col">
                                <a class="row-title">
                                    etype
                                </a>
                                <div class="row-actions">
                                    <span class="edit">
                                        <a>Edit</a>
                                        | 
                                    </span>
                                    <span>
                                        <a title="Duplicate this item">Duplicate</a> |
                                    </span>
                                    <span class="trash">
                                        <a>Trash</a>
                                    </span>
                            </div>
                        </div>
                    </div>
                </td>
                <td></td>
            </tr>
            <tr>
                <td class="wl-check-column">
                    <input type="checkbox">
                </td>

                <td colspan="3">
                    <a class="row-title">Telephone</a>
                    <br/>
                    <table class="wl-container wl-container-full wl-spaced-table">
                        <tr>
                            <td colspan="2">
                                Property Help Text
                            </td>
                            <td>
                                <input type="text" placeholder="Telephone" class="wl-form-control">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                Field Type Help Text
                            </td>
                            <td>
                                <select class="wl-form-select">
                                    <option value="-1">Custom Field</option>
                                </select> 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                Field Help Text
                            </td>
                            <td>
                                <input type="text" placeholder="Contact Form" class="wl-form-control">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                            Transform Help Text
                            </td>
                            <td>
                                <select  class="wl-form-select">
                                    <option value="-1">None</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                            <td>
                                <button class="button action bg-primary text-white">
                                    Close Mapping
                                </button>
                            </td>
                        </tr>
                    </table>
                    <div class="wl-text-right">
                    <br/><br/>
                        <button class="button action bg-primary text-white" style="margin:auto;">
                            Add Mapping
                        </button>
                    </div>
                </td>
            </tr>
        </table>
        <div class="wl-container wl-container-full">
            <div class="wl-col">
                <select  class="form-control">
                    <option value="-1">Bulk Actions</option>
                    <option value="duplicate">Duplicate</option>
                    <option value="trash">Move to Trash</option>
                </select>
            </div>
            <div class="wl-col">
                <button class="button action"> Apply </button>
            </div>
            <div class="wl-col wl-align-right">
                <button class="button action"> Save </button>
            </div>

        </div>


<!-- second mock up template end -->

