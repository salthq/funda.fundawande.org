jQuery(document).ready(function($) {
  $("#chooseType").on("change", function() {
    if (this.value === "vid") {
      $("#resource-video").show();
    } else {
      $("#resource-video").hide();
    }
    if (this.value === "pdf") {
      $("#resource-pdf").show();
    } else {
      $("#resource-pdf").hide();
    }
  });
});
