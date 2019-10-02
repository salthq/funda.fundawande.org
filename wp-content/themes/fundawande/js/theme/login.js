jQuery(document).ready( function($) {

    var form = document.getElementById('fw-registration-form');
    if (form) {
    form.addEventListener("submit", function(event){
            if (grecaptcha.getResponse() === '') {
                event.preventDefault();
                alert('Please check the recaptcha');
            }
        }
        , false);
    }
});
