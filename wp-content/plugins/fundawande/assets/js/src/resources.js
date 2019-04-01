jQuery(document).ready(function($) {
  // Show the fields for the selected option when the editor screen loads
  if ($("#resource_type").children("option:selected") === "video") {
    $("#resource-video").show();
    $("#resource-pdf").hide();
  } else {
    $("#resource-pdf").show();
    $("#resource-video").hide();
  }

  // Change the visible resource type fields based on the type selected
  $("#resource_type").on("change", function() {
    if (this.value === "Video") {
      $("#resource-video").show();
    } else {
      $("#resource-video").hide();
    }
    if (this.value === "PDF") {
      $("#resource-pdf").show();
    } else {
      $("#resource-pdf").hide();
    }
  });
});
