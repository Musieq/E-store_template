const showFormButton = document.getElementById('my-account-show-login-form');
function showLoginMenu(formBackground, formContainer) {
    formBackground.classList.add('my-account-show')
    formContainer.classList.add('my-account-show')

}
function hideLoginMenu(formBackground, formContainer) {
    formBackground.classList.remove('my-account-show');
    formContainer.classList.remove('my-account-show');
}
function showFormButtonExists() {
    if(showFormButton){
        const formBackground = document.querySelector('.my-account-login-background');
        const formContainer = document.querySelector('.my-account-login-wrap');
        const formContainerChild = document.querySelector('.my-account-login-form-wrap');
        //const formExitButton = document.querySelector('.tab-exit');
        showFormButton.addEventListener('click', function(e) {
            e.preventDefault();
            showLoginMenu(formBackground, formContainer)
        });
        formContainer.addEventListener('click', function() {hideLoginMenu(formBackground, formContainer)});
        //formExitButton.addEventListener('click', function() {hideLoginMenu(formBackground, formContainer)});
        formContainerChild.addEventListener('click', function (e) { e.stopPropagation() })
    }
}
showFormButtonExists();