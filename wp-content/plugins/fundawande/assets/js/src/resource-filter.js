jQuery(document).ready(function($) {
  // Filter the resources shown based on the category selected
  $('[name="filter-resources"]').change(function() {
    var cat = $(this).data("cat");
    console.log(cat);
    $("#resource-list").fadeOut();
    // $('#loadingResults').show();
    $.ajax({
      url: fundawande_ajax_object.ajaxurl,
      data: {
        action: "resource_filter_ajax_request",
        category: cat
      },
      success: function(items) {
        // $("#loadingResults").hide();
        console.log(items);
        $("#resource-list")
          .empty()
          .append(items)
          .fadeIn();
      },
      error: function(errorThrown) {
        console.log(errorThrown);
      }
    });
  });
});
