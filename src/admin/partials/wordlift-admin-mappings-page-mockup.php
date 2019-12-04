<!DOCTYPE html>
<html>

    <style>
    table {
        border-collapse: collapse;
        border-spacing: 0;
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
    .full-width {
        width: 100%;
    }
    .text-center{
        text-align:center!important;
    }
    .text-left{
        text-align:left !important;
    }
    .text-right{
        text-align:right !important;
    }
    .align-right {
        align-self: right;
    }
    .row   {
        display: flex;
        flex: 1;
    }
    .col {
        padding: 1em; 
    }
    .mockup-table-1 {
        margin-right: 10px;
        width: 90% !important;
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
    .small-table {
        width: 500px;
        
    }
    .large-div {
        width: 700px;
    }
    .hide {
        display: none;
    }

    .page-title-action:active {
        background-color: #0073aa;
        color: #f7f7f7;
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

        <h1 class="wp-heading-inline">
        Mappings </h1>
            <table class="wp-list-table widefat fixed striped mockup-table-1">
                <thead>
                    <tr class="table-row">
                        <td>
                            <div class="row">
                                <div class="col">
                                    <input type="checkbox">
                                </div>
                                <div class="col">

                                    Title
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
                                My custom post type
                                
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
                                Another custom post type

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
                                    Title
                                </div>
                            </div>
                        </td>
                    </tr>
                </tfoot>

            </table>
        </div>

        <div class="tablenav bottom">
            <div class="alignleft actions bulkactions">
            <label for="bulk-action-selector-bottom" class="screen-reader-text">Select bulk action</label><select name="action2" id="bulk-action-selector-bottom">
            <option value="-1">Bulk Actions</option>
            <option value="acfduplicate" class="hide-if-no-js">Duplicate</option>
            <option value="trash">Move to Trash</option>
            </select>
            <input type="submit" id="doaction2" class="button action" value="Apply">
            </div>
        </div>



    </div>



    <div id="second_mockup" class="large-div hide">
        <br/>
        <div class="text-left">
            <a onclick="show_first_mockup()" href="#">Go Back</a>
        </div>
        <h2> Edit Mapping </h2>
        
        <input type="text"  placeholder="Edit Title" class="form-control">
        <br/><br/>
         <div class="bg-primary text-white container">
            Rules
         </div>
        <div class="row border-full">
           <div class="col">
                Here we show the help text
            </div>
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
            </div>
        </div>
        <br/>
        <table class="full-width">
            <tr class="row bg-primary text-white container">
                <td class="full-width">
                    <input type="checkbox">
                    Property
                </td>
                <td class="full-width">
                    Field
                </td>
            </tr>
            <tr>
                <td>
                    <div class="row border-full">
                        <div class="col">
                            <input type="checkbox">
                        </div>
                        <div class="col">
                            etype
                            <br/>
                            <b>
                                <a>
                                    Edit
                                </a>|
                                <a>
                                    Duplicate
                                </a>|
                                <a>
                                    Trash
                                </a>|
                            </b>
                        </div>
                    </div>        
                </td>
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