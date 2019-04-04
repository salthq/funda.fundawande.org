jQuery(document).ready(function($) {
  function resourceFilterAjaxRequest(cat) {
    $("#resource-list").fadeOut("fast");
    $("#loadingResults").show();
    $.ajax({
      url: fundawande_ajax_object.ajaxurl,
      data: {
        action: "resource_filter_ajax_request",
        category: cat
      },
      success: function(items) {
        $("#loadingResults").hide();
        $("#resource-list")
          .empty()
          .append(items)
          .fadeIn();
        setupVideoPlayers();
      },
      error: function(errorThrown) {
        console.log(errorThrown);
      }
    });
  }

  // Filter the resources shown based on the category selected
  $('[name="filter-resources"]').change(function() {
    var cat = $(this).data("cat");
    resourceFilterAjaxRequest(cat);
  });
});

function expandResource(index) {
  jQuery(document).ready(function($) {
    $("#fullDescription" + index).removeClass("d-none");
    $("#excerptDescription" + index).addClass("d-none");
    $("#fullDescription" + index).slideToggle("slow");
  });
}

function minimiseResource(index) {
  jQuery(document).ready(function($) {
    $("#excerptDescription" + index).removeClass("d-none");
    $("#fullDescription" + index).slideToggle("slow");
    $("#fullDescription" + index).addClass("d-none");
  });
}
