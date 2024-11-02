
document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector("form");
    const usernameInput = document.getElementById("username");
    const emailInput = document.getElementById("email");
    const mobileInput = document.getElementById("mobile");
    const passwordInput = document.getElementById("password");
    const errorBox = document.getElementById("error-box");

    form.addEventListener("submit", function(event) {
        errorBox.textContent = "";
        let errors = [];

        if (usernameInput.value.trim() === "") {
            errors.push("Username is required.");
        }
        if (emailInput.value.trim() === "") {
            errors.push("Email is required.");
        }
        if (mobileInput.value.trim() === "") {
            errors.push("Mobile number is required.");
        }
        if (passwordInput.value.trim() === "") {
            errors.push("Password is required.");
        }

        if (usernameInput.value.length < 4) {
            errors.push("Username must be at least 4 characters long.");
        }

        if (passwordInput.value.length < 5) {
            errors.push("Password must be at least 5 characters long.");
        }

        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!emailPattern.test(emailInput.value)) {
            errors.push("Please enter a valid email address.");
        }

        if (!/^\d+$/.test(mobileInput.value)) {
            errors.push("Mobile number must contain only digits.");
        }

        if (errors.length > 0) {
            event.preventDefault();
            errorBox.textContent = errors[0];
            errorBox.style.color = "red";
        }
    });
});
