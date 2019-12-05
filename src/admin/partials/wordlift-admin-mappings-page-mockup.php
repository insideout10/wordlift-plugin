<!DOCTYPE html>
<html>

    <style>
    table {
        border-collapse: collapse;
        border-spacing: 0;
    }
    .wl-mappings-heading-text {
        font-size: 23px;
        font-weight: 400;
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

    .form-control {
        padding: 5px;
        margin: 5px;
        width: -webkit-calc(100% - 10px);
        width: -moz-calc(100% - 10px);
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        -o-box-sizing: border-box;
        -ms-box-sizing: border-box;
        box-sizing: border-box;
    }
    .form-control-fixed {
        width: 150px;
    }

    .row   {
        display: flex;
        flex: 1;
    }
    .col {
        padding: 1em; 
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
            document.getElementById("second_mockup").style.display = "block";
            document.getElementById("first_mockup").style.display = "none";
        }
        function show_first_mockup() {
            document.getElementById("second_mockup").style.display = "none";
            document.getElementById("first_mockup").style.display = "block";
        }
    </script>
    <div id="first_mockup">
        <h1 class="wp-heading-inline wl-mappings-heading-text">
            Mappings
        <button class="button wl-mappings-add-new" onclick="show_second_mockup()">
            Add New
        </button>
        </h1>
        <table class="wp-list-table widefat striped mockup-table-1">
            <thead>
                <tr class="table-row">
                    <td>
                        <div class="row">
                            <div class="col">
                                <input type="checkbox">
                            </div>
                            <div class="col">
                                <a class="row-title">Title</a>
                            </div>
                        </div>
                    </td>
                </tr>
            </thead>
            <tr class="table-row">
                <td>
                    <div class="row">
                        <div class="col">
                            <input type="checkbox">
                        </div>
                        <div class="col">
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
                        </div>
                    </div>
                </td>
            </tr>

            <tr class="table-row">
                <td>
                <div class="row">
                        <div class="col">
                            <input type="checkbox">
                        </div>
                        <div class="col">
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




                        </div>
                    </div>
                </td>
            </tr>
            <tfoot>
                <tr class="table-row">
                    <td>
                    <div class="row">
                            <div class="col">
                                <input type="checkbox">
                            </div>
                            <div class="col">
                                <a class="row-title">Title</a>
                            </div>
                        </div>
                    </td>
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


    <div id="second_mockup" class="hide">
        <br/>
        <div class="text-left">
            <a onclick="show_first_mockup()" href="#">Go Back</a>
        </div>
        <h1 class="wp-heading-inline wl-mappings-heading-text">
            Edit Mapping
        </h1>
        
        <input type="text" class="form-control wl-spaced  wl-input-class"
         size="30" value="My Custom Post Type" id="title" spellcheck="true" autocomplete="off">
        <br/>
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
                    <div class="col">
                    Use the mapping if
                    <div class="row">
                        <div class="col">
                            <select class="form-control-fixed">
                                <option value="-1">Post type</option>
                            </select>
                        </div>
                        <div class="col">
                            <select class="form-control-fixed">
                                <option value="-1">is equal to</option>
                            </select>
                        </div>
                        <div class="col">
                            <select class="form-control-fixed">
                                <option value="-1">Custom Post</option>
                            </select>
                        </div>
                        <div class="col">
                            <button class="button action"> And </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <select class="form-control-fixed">
                                <option value="-1">Post taxonomy</option>
                            </select>
                        </div>
                        <div class="col">
                            <select class="form-control-fixed">
                                <option value="-1">is equal to</option>
                            </select>
                        </div>
                        <div class="col">
                            <select  class="form-control-fixed">
                                <option value="-1">My Term</option>
                            </select>
                        </div>
                        <div class="col">
                            <button class="button action"> And </button>
                        </div>
                    </div>
                    <div class="row  ">
                        <div class="col">
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
                    <th colspan="0">
                        <input type="checkbox">&nbsp;
                    </th>
                    <th colspan="2">
                        Property
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
                    <div class="row">
                            <div class="col">
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
                        <div class="col">
                            <input type="checkbox">
                        </div>
                        <div class="col">
                            Telephone
                            <br/>
                            <div class="row">
                                <div class="col full-width">
                                    Property Help Text
                                </div>
                                <div class="col">
                                    <input type="text" placeholder="Telephone"  class="form-control-fixed">                    
                                </div>
                            </div>
                            <br/>
                            <div class="row">
                                <div class="col  full-width">
                                    Field Type Help Text
                                </div>
                                <div class="col ">
                                    <select  class="form-control-fixed">
                                        <option value="-1">Custom Field</option>
                                    </select>                  
                                </div>
                            </div>
                            <div class="row">
                                <div class="col  full-width">
                                    Field  Help Text
                                </div>
                                <div class="col">
                                    <input type="text" placeholder="Contact Form" class="form-control-fixed">                  
                                </div>
                            </div>
                            <br/>
                            <div class="row">
                                <div class="col  full-width">
                                    Transform Help Text
                                </div>
                                <div class="col">
                                    <select  class="form-control-fixed">
                                        <option value="-1">None</option>
                                    </select>                  
                                </div>
                            </div>
                            <br/>
                            <div class="row">
                                <div class="col  full-width">
                                    
                                </div>
                                <div class="col">
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
                        <div class="col">
                            <select  class="form-control">
                                <option value="-1">Bulk Actions</option>
                                <option value="duplicate">Duplicate</option>
                                <option value="trash">Move to Trash</option>
                            </select>
                        </div>
                        <div class="col">
                            <button class="button action"> Apply </button>
                        </div>
                        <div class="col">
                            <button class="button action"> Save </button>
                        </div>
                    </div>
                </td>
            </tr>
        </table>


    </div>

</html>