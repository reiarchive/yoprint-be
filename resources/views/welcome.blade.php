<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload and Table</title>
    <style>
        /* Add some basic CSS styles for the header and table */
        body {
            font-family: Arial, sans-serif;
        }

        .header {
            text-align: center;
            background-color: #f0f0f0;
            padding: 20px;
        }

        .table-container {
            margin: 20px;
            border: 1px solid #ccc;
            padding: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
    @vite('resources/css/app.css')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.35/moment-timezone-with-data.min.js"></script>

</head>
<body>
    <div class="header">
        <h1>File Upload</h1>
        <p>Select file/Drag and drop</p>
        @csrf
        <input type="file" name="file" id="fileInput">
        <button id="uploadButton">UPLOAD</button>
        <br>
        <br>
        <span id="upload-status"></span>
    </div>
    <div class="table-container">
        <h2>Uploaded Files</h2>
        <table id="table-files">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>File Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($uploads as $upload)
                    <tr data-id="{{ $upload->id }}">
                        <td>{{ $upload->created_at }}</td>
                        <td>{{ $upload->file_name }}</td>
                        <td class="status">{{ $upload->status }}</td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>
    @vite('resources/js/app.js')

    <script>
        $(document).ready(function() {
            $('#uploadButton').on('click', () => {

                // Create a FormData object to store the file data
                var formData = new FormData();
                var fileInput = $('#fileInput')[0].files[0];

                if (fileInput) {
                    formData.append('file', fileInput);
                    var csrfToken = $(this).find('input[name="_token"]').val();

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    // Make an AJAX request to your API
                    $.ajax({
                        url: '/upload',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        beforeSend: function() {
                            $("#upload-status").text("Loading...");
                        },
                        success: function(response) {
                            $("#upload-status").text("");

                            if(response.error == 1) {
                                alert(response.message);
                            }
                        },
                        error: function(error) {
                            $("#upload-status").text("Error uploading file:", error);
                            console.error("Error uploading file:", error);
                        }
                    });
                } else {
                    $("#upload-status").text("No file selected");
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            Echo.channel('filestatus-channel')
                .listen('fileStatus', (e) => {
                    if (e.data) {
                        if (e.data.isNew) {

                            var inputMoment = moment.tz(e.data.time, 'UTC');

                            var formattedDateTime = inputMoment.format('YYYY-MM-DD HH:mm:ss');

                            var newRow = "<tr data-id=\"" + e.data.id + "\">";
                            newRow += "<td>" + formattedDateTime + "</td>";
                            newRow += "<td>" + e.data.file_name + "</td>";
                            newRow += "<td class=\"status\">" + e.data.status + "</td>";
                            newRow += "</tr>";

                            // Append the new row to the table's tbody
                            $("#table-files tbody").append(newRow);
                        } else {
                            console.log(e.data.id);
                            console.log(e.data.status);

                            var $row = $("tr[data-id='" + e.data.id + "']");

                            // Update the status cell within the <tr> element
                            $row.find(".status").text(e.data.status);
                        }
                    }

                });
        });
    </script>

</body>

</html>
