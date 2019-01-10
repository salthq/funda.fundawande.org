jQuery(document).ready( function($) {

    // TODO: Delete line 5 and uncomment line 4 once language switching is re-enabled
    // $('#set-language-preference').modal('show');
    $('#terms-and-conditions').modal('show');

    // //Once the continue button is clicked on the language preference modal, the Ts & Cs modal fires.
    // TODO: Uncomment the lines below once language switching is re-enabled
    // $('#set-language-preference').click(function() {
    //     $('#language-preference').modal('hide');
    //     $('#terms-and-conditions').modal('show');
    // });

    //Trigger a reload when the terms and conditions are signed, so the user is directed to their course
    $('#go-to-course').click(function() {
        location.reload('true');
    });

});