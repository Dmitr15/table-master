const form = document.getElementById("form");

const name_input = document.getElementById("firstname-input");
const email_input = document.getElementById("email-input");
const password_input = document.getElementById("password-input");
const repeat_input = document.getElementById("repeat-password-input");
const error_message = document.getElementById("error-message");
const allInputs = [name_input, email_input, password_input, repeat_input];

form.addEventListener('submit', (e) => {
    let errors = [];

    if (name_input) {
        errors = getSignupFormErrors(name_input.value, email_input.value, password_input.value, repeat_input.value);
    } else {
        errors = getLoginFormErrors(email_input.value, password_input.value);
    }

    if (errors.length > 0) {
        e.preventDefault();
        error_message.innerText = errors.join("\n ");
    }
})

function getLoginFormErrors(email, password) {
    let errors = [];

    if (email === '' || email === null) {
        errors.push('Email is required!');
        email_input.parentElement.classList.add('incorrect');
    }
    if (password !== '' && password.length < 7) {
        errors.push('Password must be more then 7 characters!');
        password_input.parentElement.classList.add('incorrect');
    }
    if (password === '' || password === null) {
        errors.push('Password is required!');
        password_input.parentElement.classList.add('incorrect');
    }

    return errors;
}

function getSignupFormErrors(name, email, password, repeat) {
    let errors = [];

    if (name === '' || name === null) {
        errors.push('Firstname is required!');
        name_input.parentElement.classList.add('incorrect');
    }
    if (email === '' || email === null) {
        errors.push('Email is required!');
        email_input.parentElement.classList.add('incorrect');
    }
    if (password !== '' && password.length < 7) {
        errors.push('Password must be more then 7 characters!');
        password_input.parentElement.classList.add('incorrect');
    }
    if (password === '' || password === null) {
        errors.push('Password is required!');
        password_input.parentElement.classList.add('incorrect');
    }
    if (repeat === '' || repeat === null) {
        errors.push('Please repeat password!');
        repeat_input.parentElement.classList.add('incorrect');
    }
    if (repeat !== password) {
        errors.push('Please repeat the same password!');
        repeat_input.parentElement.classList.add('incorrect');
        password_input.parentElement.classList.add('incorrect');
    }
    return errors;
}



allInputs.forEach(input => {
    input.addEventListener('input', () => {
        if (input.parentElement.classList.contains('incorrect')) {
            input.parentElement.classList.remove('incorrect');
            error_message.innerText = '';
        }
    });
});