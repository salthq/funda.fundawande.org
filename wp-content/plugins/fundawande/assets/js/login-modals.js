jQuery(document).ready( function($) {
    $('#language-preference').modal('show');

    //Once the continue button is clicked on the language preference modal, the Ts & Cs modal fires.
    $('#set-language-preference').click(function() {
        $('#language-preference').modal('hide');
        $('#terms-and-conditions').modal('show');
    });

    //Trigger a reload when the terms and conditions are signed, so the user is directed to their course
    $('#go-to-course').click(function() {
        location.reload('true');
    });

});