jQuery(document).ready( function($) {

    // Mark lesson complete and continue functionality
    $('#lesson-complete').click(function(e) {
        e.preventDefault();
        console.log('completing');
        var userID = $(this).data('user-id');
        var postID = $(this).data('post-id');
        var url = $(this).data('url');
        $.ajax({
            type: 'POST',
            url: fundawande_ajax_object.ajaxurl,
            data: {
                'action':'fw_lesson_complete',
                userid: userID,
                postid: postID
            },
            success:function(data) {
                console.log(data);
                if (url !== undefined) {
                    window.location = url;
                }

            },
            error: function(errorThrown){
                console.log(errorThrown);
            }
        });
    });

    //Hide the minimized progress component on click and slide the expanded component in from the left
    $('#sidebar-minimized').click(function() {
        $('#sidebar-expanded').animate({'margin-left': '0px'});
        $('.lesson-sidebar-expanded').removeClass('lesson-sidebar-absolute')
        $('.lesson-sidebar-expanded').addClass('lesson-sidebar-fixed')
        $('#sidebar-minimized').hide();

    });

    //Slide the expanded progress component back out on click and show the minimized progress component
    $('#sidebar-expanded').click(function() {
        hideSidebar();
    });

    function hideOverlay () {
        $('#overlay').click(function () {
            $(this).remove();
            hideSidebar();

        });
    };


    function hideSidebar () {
        $('#sidebar-minimized').show("medium");
        $('#sidebar-expanded').animate({'margin-left': '-500px'});
    }

    //If there is both a custom feedback modal and an end of unit modal, 
    $('#end-unit-modal-link').click(function() {
        $('#end-lesson-modal').hide();
    });



    //When the user clicks the minimized sidebar, create an overlay
    $('#sidebar-minimized').click(function() {
        var docHeight = $(document).height();
     
        $("body").append("<div id='overlay'></div>");
     
        $("#overlay")
           .height(docHeight)
           .css({
              'opacity' : 0.4,
              'position': 'fixed',
              'top': 0,
              'left': 0,
              'background-color': '#000',
              'width': '100%',
              'z-index': 5
           });

        hideOverlay();
    });

    $('#sidebar-expanded').click(function() {
        $("#overlay").remove();
        $('.lesson-sidebar-expanded').addClass('lesson-sidebar-absolute')
        $('.lesson-sidebar-expanded').removeClass('lesson-sidebar-fixed')
    });

    //The sidebar must changed between absolute and fixed positionining depending on scroll position
    $(window).scroll(function() {
        var scroll = $(window).scrollTop(); 

        if(scroll >= 160) {
            $('.lesson-sidebar-minimized').addClass('lesson-sidebar-fixed')
            $('.lesson-sidebar-minimized').removeClass('lesson-sidebar-absolute')
        }
        else {
            $('.lesson-sidebar-minimized').addClass('lesson-sidebar-absolute')
            $('.lesson-sidebar-minimized').removeClass('lesson-sidebar-fixed')
        }
        
    })

});
