jQuery(document).ready( function($) {


    $('#review-assessment button[type="submit"]').on('click', function(e) {

        e.preventDefault();
        tinyMCE.triggerSave();
        $.each($('.question-block'), function (i, val) {
            var text = $(this).find('.wp-editor-area').val();
            $('[name="questions['+i+'][feedback]"]').val(text);
            console.log(text);
        });


        $('form#review-assessment').submit();
    });

    
});