<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Process Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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
            position: relative; /* Ensure relative positioning for child elements */
        }

        /* Style for close button */
        .close {
            position: absolute;
            top: 10px;
            right: 10px; /* Adjust the right position as needed */
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            transition: color 0.3s;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
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

        /* Style for table */
        #processTable_wrapper {
            padding: 20px;
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
        <th>Edit</th>
        <th>Delete</th>

    </tr>
    </thead>
    <tbody>
    <!-- Process data will be loaded dynamically using AJAX -->
    </tbody>
</table>
<!-- Button to open modal for adding new process -->
<button class="btn btn-success mb-4" id="openModalBtn">Create New Process</button>
<!-- Modal for adding new process -->
<!-- Modal for adding new process -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Add New Process</h2>
        <form id="createProcessForm">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="mb-3">
                <label for="process_name" class="form-label">Process Name:</label>
                <input type="text" class="form-control" id="process_name" name="process_name" placeholder="Enter Process Name" required>
            </div>
            <div class="mb-3">
                <label for="process_owner" class="form-label">Process Owner:</label>
                <input type="text" class="form-control" id="process_owner" name="process_owner" placeholder="Enter Process Owner" required>
            </div>
            <div class="mb-3">
                <label for="prcdept_name" class="form-label">Department Name:</label>
                <input type="text" class="form-control" id="prcdept_name" name="prcdept_name" placeholder="Enter Department Name" required>
            </div>
            <div class="mb-3">
                <label for="prc_desc" class="form-label">Description:</label>
                <textarea class="form-control" id="prc_desc" name="prc_desc" placeholder="Enter Description" required></textarea>
            </div>
            <div class="mb-3">
                <label for="prc_doc" class="form-label">Document:</label>
                <input type="file" class="form-control" id="prc_doc" name="prc_doc" accept=".pdf, .doc, .docx" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Process</button>
        </form>
    </div>
</div>
<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Edit Process</h2>
        <form id="editProcessForm">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" id="edit_process_id" name="process_id"> <!-- Hidden input for process ID -->
            <div class="mb-3">
                <label for="edit_process_name" class="form-label">Process Name:</label>
                <input type="text" class="form-control" id="edit_process_name" name="process_name" placeholder="Enter Process Name" required>
            </div>
            <div class="mb-3">
                <label for="edit_process_owner" class="form-label">Process Owner:</label>
                <input type="text" class="form-control" id="edit_process_owner" name="process_owner" placeholder="Enter Process Owner" required>
            </div>
            <div class="mb-3">
                <label for="edit_prcdept_name" class="form-label">Department Name:</label>
                <input type="text" class="form-control" id="edit_prcdept_name" name="prcdept_name" placeholder="Enter Department Name" required>
            </div>
            <div class="mb-3">
                <label for="edit_prc_desc" class="form-label">Description:</label>
                <textarea class="form-control" id="edit_prc_desc" name="prc_desc" placeholder="Enter Description" required></textarea>
            </div>
            <div class="mb-3">
                <label for="edit_prc_doc" class="form-label">Document:</label>
                <input type="file" class="form-control" id="edit_prc_doc" name="prc_doc" accept=".pdf, .doc, .docx">
            </div>
            <button type="submit" class="btn btn-primary">Update Process</button>
        </form>
    </div>
</div>


<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Delete Process</h2>
        <p>Are you sure you want to delete this process?</p>
        <div class="text-center">
            <button id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
            <button id="cancelDeleteBtn" class="btn btn-secondary">Cancel</button>
        </div>
    </div>
</div>



<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.4.4/dist/qrcode.min.js"></script> <!-- Import QR code library -->


<script type="text/javascript">
    $(document).ready(function() {




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


        // Include CSRF token in AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Open modal for creating a new process
        $("#openModalBtn").click(function() {
            $("#myModal").css("display", "block");
        });

        // Close modals when clicking on the close button or outside of modal content
        $(".close, #myModal").click(function(event) {
            if (event.target == $("#myModal")[0] || $(event.target).hasClass('close')) {
                $(".modal").css("display", "none");
            }
        });

        // Function to handle opening the edit modal and populating data
        $(document).on('click', '.editBtn', function() {
            var processId = $(this).data('id');

            $.ajax({
                url: "/processes/" + processId + "/edit",
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#edit_process_id').val(response.process_id);
                    $('#edit_process_name').val(response.process_name);
                    $('#edit_process_owner').val(response.process_owner);
                    $('#edit_prcdept_name').val(response.prcdept_name);
                    $('#edit_prc_desc').val(response.prc_desc);

                    $("#editModal").css("display", "block");
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

        // Handle form submission for updating process data
        $("#editProcessForm").submit(function(event) {
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            var processId = $('#edit_process_id').val();

            $.ajax({
                url: "/processes/" + processId,
                method: 'PUT',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $("#editModal").css("display", "none");
                    loadProcesses();
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

        // Function to load processes into the table
        function loadProcesses() {
            $.ajax({
                url: "{{ route('process.index') }}",
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    table.clear().draw(); // Clear existing data in the table

                    $.each(response, function(index, process) {
                        var fileName = process.prc_doc.split('/').pop();
                        var qrCodePath = '{{ asset("/qrcodes") }}/' + process.process_name + '.png';

                        var newRow = [
                            process.process_name,
                            process.process_owner,
                            process.prcdept_name,
                            process.prc_desc,
                            '<a href="/storage/documents/' + fileName + '" download="' + fileName + '">Download</a>',
                            '<img src="' + qrCodePath + '" class="img-fluid rounded" alt="QR Code" style="max-width: 100px; max-height: 100px;">',
                            '<button class="btn btn-primary btn-sm editBtn" data-id="' + process.process_id + '">Edit</button>',
                            '<button class="btn btn-danger btn-sm deleteBtn" data-id="' + process.process_id + '">Delete</button>'
                        ];
                        table.row.add(newRow).draw();
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error:", xhr.responseText);
                }
            });
        }

        // Load processes on page load
        loadProcesses();

        // Function to handle deleting a process
        // function deleteProcess(id) {
        //     if (confirm("Are you sure you want to delete this process?")) {
        //         $.ajax({
        //             url: "/processes/" + id,
        //             method: 'DELETE',
        //             success: function(response) {
        //                 // Reload processes after deletion
        //                 loadProcesses();
        //             },
        //             error: function(xhr, status, error) {
        //                 console.error(xhr.responseText);
        //             }
        //         });
        //     }
        // }

// Add event listener to the delete button
//         $(document).on('click', '.deleteBtn', function() {
//             var processId = $(this).data('id');
//             deleteProcess(processId);
//         });


        // Add event listener to the delete button
        $(document).on('click', '.deleteBtn', function() {
            var processId = $(this).data('id');
            $('#deleteModal').data('processId', processId); // Store process ID in modal data
            $('#deleteModal').css('display', 'block'); // Open the delete modal
        });

// Handle confirm delete button click
        $('#confirmDeleteBtn').click(function() {
            var processId = $('#deleteModal').data('processId'); // Retrieve process ID from modal data
            deleteProcess(processId); // Call the delete function with process ID
            $('#deleteModal').css('display', 'none'); // Close the delete modal
        });

// Handle cancel delete button click
        $('#cancelDeleteBtn').click(function() {
            $('#deleteModal').css('display', 'none'); // Close the delete modal
        });


        function deleteProcess(id) {
            $.ajax({
                url: "/processes/" + id,
                method: 'DELETE',
                success: function(response) {
                    // Reload processes after deletion
                    loadProcesses();
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }


    });
</script>






</body>
</html>
