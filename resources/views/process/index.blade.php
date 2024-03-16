<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Management</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
    <style>
        /* Style for modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);
            border-radius: 10px;
        }

        /* Style for button */
        .button {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 8px;
        }
    </style>
</head>
<body>
<h1>Processes</h1>
<!-- Table for displaying processes -->
<table id="processTable" class="display">
    <thead>
    <tr>
        <th>Process Name</th>
        <th>Process Owner</th>
        <th>Department Name</th>
        <th>Description</th>
        <th>Document</th>
        <th>QR Code</th>
    </tr>
    </thead>
    <tbody>
    <!-- Process data will be loaded dynamically using AJAX -->
    </tbody>
</table>
<!-- Button to open modal for adding new process -->
<button class="button" id="openModalBtn">Create New Process</button>
<!-- Modal for adding new process -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Add New Process</h2>
        <form id="createProcessForm">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group">
                <label for="process_name">Process Name:</label>
                <input type="text" id="process_name" name="process_name" placeholder="Enter Process Name" required>
            </div>
            <div class="form-group">
                <label for="process_owner">Process Owner:</label>
                <input type="text" id="process_owner" name="process_owner" placeholder="Enter Process Owner" required>
            </div>
            <div class="form-group">
                <label for="prcdept_name">Department Name:</label>
                <input type="text" id="prcdept_name" name="prcdept_name" placeholder="Enter Department Name" required>
            </div>
            <div class="form-group">
                <label for="prc_desc">Description:</label>
                <textarea id="prc_desc" name="prc_desc" placeholder="Enter Description" required></textarea>
            </div>
            <div class="form-group">
                <label for="prc_doc">Document:</label>
                <input type="file" id="prc_doc" name="prc_doc" accept=".pdf, .doc, .docx" required>
            </div>

            <!-- Hidden input field for storing data for QR code -->
            <input type="hidden" id="prc_QR_data" name="prc_QR_data">

            <!-- No need for input field for QR code -->

            <button type="submit">Add Process</button>
        </form>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.4.4/dist/qrcode.min.js"></script> <!-- Import QR code library -->


<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#processTable').DataTable();

        // Button to open modal
        $("#openModalBtn").click(function() {
            $("#myModal").css("display", "block");
        });

        // Close modal when click on close button
        $(".close").click(function() {
            $("#myModal").css("display", "none");
        });

        // Close modal when click outside of modal content
        $(window).click(function(event) {
            if (event.target == $("#myModal")[0]) {
                $("#myModal").css("display", "none");
            }
        });

        // Function to generate QR code dynamically
        function generateQRCode(data, containerId) {
            // Generate QR code
            new QRCode(document.getElementById(containerId), {
                text: data,
                width: 200,
                height: 200
            });
        }

        // AJAX to submit form data and add new process
        $("#createProcessForm").submit(function(event) {
            event.preventDefault();
            var formData = new FormData($(this)[0]); // Create FormData object for file upload
            $.ajax({
                url: "{{ route('process.store') }}",
                method: 'POST',
                data: formData,
                contentType: false, // Ensure that jQuery does not add a Content-Type header
                processData: false, // Prevent jQuery from converting the FormData object to a string
                success: function(response) {
                    $("#myModal").css("display", "none");
                    loadProcesses(); // Reload process table
                    $("#createProcessForm")[0].reset(); // Clear form
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });


        function loadProcesses() {
            $.ajax({
                url: "{{ route('process.index') }}",
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log("Success:", response); // Log the response data to check if it's retrieved correctly
                    table.clear(); // Clear existing data in the table
                    $.each(response, function(index, process) {
                        // Extract the file name from the file path
                        var fileName = process.prc_doc.split('/').pop();

                        // Construct the path for the QR code image
                        var qrCodePath = '{{ asset("/qrcodes") }}/' + process.process_name + '.png';

                        // Add each row of data to the DataTable
                        table.row.add([
                            process.process_name,
                            process.process_owner,
                            process.prcdept_name,
                            process.prc_desc,
                            '<a href="/storage/documents/' + fileName + '" download="' + fileName + '">Download</a>', // Display link to the document
                            '<img src="' + qrCodePath + '"  alt="QR Code" width="100" height="100">' // Display the QR code image
                        ]);
                    });
                    table.draw(); // Redraw the DataTable to reflect the changes
                },
                error: function(xhr, status, error) {
                    console.error("Error:", xhr.responseText); // Log any errors
                }
            });
        }





        // Load processes on page load
        loadProcesses();
    });
</script>




</body>
</html>
