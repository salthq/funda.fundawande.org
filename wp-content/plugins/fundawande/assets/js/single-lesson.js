jQuery(document).ready( function($) {

    // Mark lesson complete and continue functionality
    $('#lesson-complete').click(function(e) {
        e.preventDefault();

        var lessonKey = $(this).data('lesson-key');
        var userID = $(this).data('user-id');
        var postID = $(this).data('post-id');
        var url = $(this).data('url');
        $.ajax({
            type: 'POST',
            url: fundawande_ajax_object.ajaxurl,
            data: {
                'action':'fw_lesson_complete',
                lessonkey: lessonKey,
                userid: userID,
                postid: postID
            },
            success:function(data) {
                console.log(data);

            },
            error: function(errorThrown){
                console.log(errorThrown);
            }
        });
    });


});
