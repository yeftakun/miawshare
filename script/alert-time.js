// Function to hide messages after a specified duration
function hideMessages() {
    var messages = document.querySelectorAll('.alert, .done'); // Select all alert and done messages
    messages.forEach(function(message) {
        setTimeout(function() {
            message.style.display = 'none'; // Hide the message
        }, 4000); // Set timeout for 3 seconds (3000 milliseconds)
    });
}

// Call hideMessages function when the page is loaded
window.onload = function() {
    hideMessages();
};