jQuery(document).ready( function($) {
    Intercom('update', {
        "hide_default_launcher": false
    });

    var form = document.getElementById('fw-registration-form');
    form.addEventListener("submit", function(event){
            if (grecaptcha.getResponse() === '') {
                event.preventDefault();
                alert('Please check the recaptcha');
            }
        }
        , false);
});
