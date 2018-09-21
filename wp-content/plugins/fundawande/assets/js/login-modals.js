jQuery(document).ready( function($) {
    $('#language-preference').modal('show');

    //Once the continue button is clicked on the language preference modal, the Ts & Cs modal fires.
    $('#set-language-preference').click(function() {
        $('#language-preference').modal('hide');
        $('#terms-and-conditions').modal('show');
    });

    //Force a page reload so the user is properly redirected to their current course page after signing Ts and Cs
    $('#go-to-course').click(function() {
        location.reload();
    });

});