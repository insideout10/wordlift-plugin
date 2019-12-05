<!DOCTYPE html>
<html>

    <style>

    .wl-mappings-heading-text {
        font-size: 23px;
        font-weight: 400;
    }
    .wl-table {
        width: 70%;
    }
    .wl-check-column {
        vertical-align: inherit;
        width: 2.2em;
    }
    .wl-table td input{
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
    .wl-spaced {
        margin-right: 10px;
        width: 95% !important;
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
        padding-left: 5px;
        min-width: 50%;
    }

    .wl-container {
        display: flex;
        justify-content: flex-start;
    }

    .wl-col {
        padding: 1em;
    }

    .row   {
        display: flex;
        flex: 1;
    }

    .bg-primary{
        background-color:#007bff!important
    }
    .text-white {
        color: #fff !important;
    }
    .container {
        padding: 5px;
    }
    .hide {
        display: none;
    }

    </style>
    <script>
        function show_second_mockup() {
            document.getElementById("second_mockup").style.display = "fixed";
            document.getElementById("first_mockup").style.display = "hidden";
        }
        function show_first_mockup() {
            document.getElementById("second_mockup").style.display = "none";
            document.getElementById("first_mockup").style.display = "block";
        }
    </script>

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
    <div id="second_mockup">
        <div class="text-left">
            <a onclick="show_first_mockup()" href="#">Go Back</a>
        </div>
        <h1 class="wp-heading-inline wl-mappings-heading-text">
            Edit Mapping
        </h1>
        <input type="text" class="form-control wl-spaced  wl-input-class"
         size="30" value="My Custom Post Type" id="title" spellcheck="true" autocomplete="off">

        <table class="wp-list-table widefat striped wl-spaced">
            <thead>
                <tr>
                    <td colspan="0">
                        Rules
                    </td>
                    <td colspan="2">
                    </td>
                </tr>
            </thead>
           <tr>
                <td class="wl-bg-light wl-description">
                    <div>
                        Here we show the help text
                    </div>
                </td>
                <td>
                    <div class="wl-col">
                    Use the mapping if
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
                    <div class="row  ">
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


        <table class="wp-list-table widefat striped wl-spaced">
            <thead>
                <tr>
                    <th colspan="2">
                    <input type="checkbox"> Property
                    </th>
                    <th colpspan="2">
                        Field
                    </th>
                </tr>
            </thead>
            <tr>
                <td>
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
                <td>
                    <div class="row border-full">
                        <div class="wl-col">
                            <input type="checkbox">
                        </div>
                        <div class="wl-col">
                            Telephone
                            <br/>
                            <div class="wl-container">
                                <div class="col full-width">
                                    Property Help Text
                                </div>
                                <div class="wl-col">
                                    <input type="text" placeholder="Telephone"  class="  wl-form-select">                    
                                </div>
                            </div>
                            <br/>
                            <div class="wl-container">
                                <div class="col  full-width">
                                    Field Type Help Text
                                </div>
                                <div class="col ">
                                    <select  class="  wl-form-select">
                                        <option value="-1">Custom Field</option>
                                    </select>                  
                                </div>
                            </div>
                            <div class="wl-container">
                                <div class="col  full-width">
                                    Field  Help Text
                                </div>
                                <div class="wl-col">
                                    <input type="text" placeholder="Contact Form" class="  wl-form-select">                  
                                </div>
                            </div>
                            <br/>
                            <div class="wl-container">
                                <div class="col  full-width">
                                    Transform Help Text
                                </div>
                                <div class="wl-col">
                                    <select  class="  wl-form-select">
                                        <option value="-1">None</option>
                                    </select>                  
                                </div>
                            </div>
                            <br/>
                            <div class="wl-container">
                                <div class="col  full-width">
                                    
                                </div>
                                <div class="wl-col">
                                    <button class="button action">
                                        Close Mapping
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </td>
            </tr>
        </table>
        <table class="full-width">
            <tr>
                <td>
                    <div class="row full-width">
                        <div class="col align-right">
                            <button class="button action bg-primary text-white">
                                Add Mapping
                            </button>
                        </div>

                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="row full-width text-right">
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
                        <div class="wl-col">
                            <button class="button action"> Save </button>
                        </div>
                    </div>
                </td>
            </tr>
        </table>


    </div>
<!-- second mock up template end -->

</html>