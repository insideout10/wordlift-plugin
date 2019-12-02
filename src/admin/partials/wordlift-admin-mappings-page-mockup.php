<!DOCTYPE html>
<html>
    <head>
        <h2>
            Mappings
            <button class="button action">Add New</button>
        </h2>
    </head>
    <style>
    .row {
        display: flex;
        border-bottom: 1px solid #000;
    }
    .row-no-border {
        display: flex;
    }
    .col {
        padding: 1em;
    }
    .small-table {
        width: 500px;
        
    }
    </style>
    <div class="aligncenter">
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
</html>