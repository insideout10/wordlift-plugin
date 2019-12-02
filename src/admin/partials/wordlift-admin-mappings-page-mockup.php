<!DOCTYPE html>
<html>
    <head>
        <h3>
            Mappings
            <button>Add New</button>
        </h3>
    </head>
    <style>
        table {
            border-collapse: collapse;
            width: 500px;
            border: 1px solid #888;
        }

        th {
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {background-color: #f2f2f2;}
    </style>
    <div class="aligncenter">
        <table class="table-small-padding">
            <tr class="table-row">
                <td>
                    <input type="checkbox">
                </td>
                <td>
                    Title
                </td>
            </tr>

            <tr class="table-row">
                <td>
                   <input type="checkbox">
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
                </td>
            </tr>

            <tr class="table-row">
                <td>
                    <input type="checkbox">
                </td>
                <td>
                    Another custom post type
                    <br />
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
                </td>
            </tr>

            <tr>
                <td>
                    <input type="checkbox">
                </td>
                <td>
                    Title
                </td>
            </tr>
        </table>
    </div>
    <select>
        <option value="-1">Bulk Actions</option>
        <option value="duplicate">Duplicate</option>
        <option value="trash">Move to Trash</option>
    </select>
</html>