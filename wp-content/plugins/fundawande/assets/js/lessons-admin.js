jQuery(document).ready( function($) {

    $('#fw-reset-quiz').click(function (e) {
        e.preventDefault();
        console.log('Running update');
        var user_id = $(this).data('user-id');
        var post_id = $(this).data('post-id');
        $.ajax({
            type: 'POST',
            url: fundawande_ajax_object.ajaxurl,
            data: {
                'action': 'fw_reset_quiz',
                post_id: post_id,
                user_id: user_id
            },
            success: function (data) {
                // This outputs the result of the ajax request
                console.log(data);
                location.reload();
                
            },
            error: function (errorThrown) {
                console.log(errorThrown);
            }
        });
    });

});
