// Function to preview the selected image
function previewImage() {
    var preview = document.querySelector('#image-preview');
    var file = document.querySelector('input[type=file]').files[0];
    var reader = new FileReader();

    reader.onloadend = function () {
        preview.src = reader.result;
        preview.style.display = 'block'; // Show the preview image
    }

    if (file) {
        reader.readAsDataURL(file); // Read the file as a data URL
    } else {
        preview.src = ''; // Clear the preview if no file is selected
        preview.style.display = 'none'; // Hide the preview image
    }
}

// Event listener for image preview click
document.querySelector('#image-preview').addEventListener('click', function() {
    document.querySelector('input[type=file]').click();
});

// Event listener for file input change
document.querySelector('input[type=file]').addEventListener('change', previewImage);
