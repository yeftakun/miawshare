document.addEventListener('DOMContentLoaded', () => {
    const passwordInput = document.getElementById('password');
    const togglePasswordButton = document.getElementById('togglePassword');
    const loginForm = document.getElementById('loginForm');
    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');

    togglePasswordButton.addEventListener('click', () => {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        togglePasswordButton.textContent = type === 'password' ? 'Show' : 'Hide';
    });

    loginForm.addEventListener('submit', (event) => {
        event.preventDefault();
        // Simulate a registration process
        const isSuccess = Math.random() > 0.5; // Randomly succeed or fail

        if (isSuccess) {
            successMessage.style.display = 'block';
            errorMessage.style.display = 'none';
        } else {
            successMessage.style.display = 'none';
            errorMessage.style.display = 'block';
        }
    });
});
