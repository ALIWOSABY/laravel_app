<!-- resources/views/process/create.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Process</title>
</head>
<body>
<h1>Create Process</h1>
<form id="createProcessForm">
    <label for="processName">Process Name:</label>
    <input type="text" id="processName" name="processName">
    <button type="submit">Submit</button>
</form>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $('#createProcessForm').submit(function(event) {
            event.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                url: "{{ route('process.store') }}",
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    // Handle success response
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    // Handle error response
                }
            });
        });
    });
</script>
</body>
</html>
