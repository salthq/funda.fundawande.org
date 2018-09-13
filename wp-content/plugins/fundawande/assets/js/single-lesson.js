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

    //Hide the minimized progress component on click and slide the expanded component in from the left
    $('#sidebar-minimized').click(function() {
        $('#sidebar-expanded').animate({'margin-left': '0px'});
        $('#sidebar-minimized').hide();

    });

    //Slide the expanded progress component back out on click and show the minimized progress component
    $('#sidebar-expanded').click(function() {
        $('#sidebar-minimized').show("medium");
        $('#sidebar-expanded').animate({'margin-left': '-500px'});
    });  



});
