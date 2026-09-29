const form = document.getElementById('form');
const username = document.getElementById('name');
const mail = document.getElementById('webmail');
const password = document.getElementById('password');

const repassword = document.getElementById('repeat-password');
const errors_message = document.getElementById('errors-message');

form.addEventListener('submit', (e) => {
    let errors = [];

    if (username) { // Sign up logic
        errors = getSignUpErrors(username, mail, password, repassword);
    } else { 
        // errors = getLoginErrors(mail, password);
    }
    if (errors.length > 0) {
        e.preventDefault();
        errors_message.innerText = errors.join(". ");
        errors_message.style.color = "red"; 
    }
});

function getSignUpErrors(usernameEl, mailEl, passwordEl, repasswordEl) {
    let errors = [];
    const password = passwordEl.value;
    const repassword = repasswordEl.value;

    [usernameEl, mailEl, passwordEl, repasswordEl].forEach(el => el.parentElement.classList.remove('incorrect'));

    if (password !== repassword) {
        errors.push('Passwords do not match');
        repasswordEl.parentElement.classList.add('incorrect');
        passwordEl.parentElement.classList.add('incorrect');
    }
    if(password.length<8){
        errors.push('Password must have at least 8 characters');
        passwordEl.parentElement.classList.add('incorrect');
    }

    return errors;
}




