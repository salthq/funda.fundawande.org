jQuery(document).ready( function($) {

    //If an element with the ID of 'current' exists on the page,
    //scroll to that element on page load.
    if($('#current')) {
        $('html, body').animate({
            scrollTop: $('#current').offset().top - 100
        }, 500, 'linear');
    }
  
  });