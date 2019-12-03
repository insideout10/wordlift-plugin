<!DOCTYPE html>
<html>

    <style>
    .row {
        display: flex;
        border-bottom: 1px solid #888;
    }
    .text-left {
        text-align: left;
    }
    .row-no-border {
        display: flex;
    }
    .col {
        padding: 1em;
    }
    .col-border {
        padding: 1em;
        border: 2px solid #888;
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
        <div class="aligncenter">
            <h2>
                Mappings
                <button class="button action" onclick="show_second_mockup()">Add New</button>
            </h2>
            <table class="wp-list-table widefat fixed small-table">
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
                <tr class="table-row">
                    <td>
                    <div class="row">
                            <div class="col">
                                <input type="checkbox">
                            </div>
                            <div class="col">
                                My custom post type
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

                <tr class="table-row">
                    <td>
                    <div class="row">
                            <div class="col">
                                <input type="checkbox">
                            </div>
                            <div class="col">
                                Another custom post type
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

            </table>
        </div>
        <div class="row-no-border">
            <div class="col">
                <select>
                    <option value="-1">Bulk Actions</option>
                    <option value="duplicate">Duplicate</option>
                    <option value="trash">Move to Trash</option>
                </select>
            </div>
            <div class="col">
                <button class="button action"> Apply </button>
            </div>
        </div>
    </div>
    <div id="second_mockup" class="large-div hide">
        <br/>
        <div class="text-left">
            <a onclick="show_first_mockup()" href="#">Go Back</a>
        </div>
        <h2> Edit Mapping </h2>
        <input type="text"  placeholder="Edit Title">
        
         <h3> Rules </h3>
        <br/>
        <div class="row">
           <div class="col-border">
                Here we show the help text
            </div>
            <div class="col-border">
                Use the mapping if
                <div class="row-no-border">
                    <div class="col">
                        <select>
                            <option value="-1">Post type</option>
                        </select>
                    </div>
                    <div class="col">
                        <select>
                            <option value="-1">is equal to</option>
                        </select>
                    </div>
                    <div class="col">
                        <select>
                            <option value="-1">Custom Post</option>
                        </select>
                    </div>
                    <div class="col">
                        <button class="button action"> And </button>
                    </div>
                </div>
                <div class="row-no-border">
                    <div class="col">
                        <select>
                            <option value="-1">Post taxonomy</option>
                        </select>
                    </div>
                    <div class="col">
                        <select>
                            <option value="-1">is equal to</option>
                        </select>
                    </div>
                    <div class="col">
                        <select>
                            <option value="-1">My Term</option>
                        </select>
                    </div>
                    <div class="col">
                        <button class="button action"> And </button>
                    </div>
                </div>
                <div class="row-no-border">
                    <div class="col">
                        <button class="button action"> Add Rule Group </button>
                    </div>
                </div>
            </div>
        </div>
        <br/>
        <div class="row-no-border">
            <div class="col text-center">
                <input type="checkbox">
                Property
            </div>
            <div class="col text-center">
                Field
            </div>
        </div>
        <div class="row-no-border">
            <div class="col text-center">
                <input type="checkbox">
            </div>
            <div class="col text-center">
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
        <div class="row-no-border">
            <div class="col text-center">
                <input type="checkbox">
            </div>
            <div class="col text-center">
                Telephone
                <br/>
                <div class="row">
                    <div class="col">
                        Property Help Text
                    </div>
                    <div class="col">
                        <input type="text" placeholder="Telephone">                    
                    </div>
                </div>
                <br/>
                <div class="row">
                    <div class="col">
                        Field Type Help Text
                    </div>
                    <div class="col">
                        <select>
                            <option value="-1">Custom Field</option>
                        </select>                  
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        Field  Help Text
                    </div>
                    <div class="col">
                        <input type="text" placeholder="Contact Form">                  
                    </div>
                </div>
                <br/>
                <div class="row">
                    <div class="col">
                        Transform Help Text
                    </div>
                    <div class="col">
                        <select>
                            <option value="-1">None</option>
                        </select>                  
                    </div>
                </div>
                <br/>
                <div class="row">
                    <div class="col">
                        <button class="button action">
                            Close Mapping
                        </button>
                    </div>
                </div>
                <br/>
                <div class="row">
                    <div class="col">
                        <button class="button action">
                            Add Mapping
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row-no-border">
            <div class="col">
                <select>
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
    </div>

</html>