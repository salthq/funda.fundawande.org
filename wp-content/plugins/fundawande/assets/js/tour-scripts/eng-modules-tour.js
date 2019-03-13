//English Module Page Walkthrough

var tour = new Anno([
  {
    target: "#wrapper-navbar",
    position: "right",
    content: "Click 'Open Menu' to see the different menu options.",
    onShow: function() {
      jQuery(document).ready(function($) {
        $("#main-menu-modal").modal("show");
      });
    },
    onHide: function() {
      jQuery(document).ready(function($) {
        $("#main-menu-modal").modal("hide");
      });
    },
    position: {
      top: "8em",
      left: "17em"
    },
    buttons: [
      {
        text: "Next",
        className: "pulse",
        click: function() {
          return this.switchToChainNext();
        }
      }
    ]
  },
  {
    target: "#module-card",
    position: "bottom",
    content:
      "These cards show you information about each module. Click on 'view module' to see the units within that module",
    buttons: [
      {
        text: "Back",
        className: "anno-btn-low-importance",
        click: function() {
          return this.switchToChainPrev();
        }
      },
      {
        text: "Done",
        className: "pulse",
        click: function() {
          return this.hide();
        }
      }
    ],
    //Without removing the 'w-100' bootstrap class, the module card stretches across the whole screen while it is active on the walkthrough.
    onShow: function() {
      jQuery(document).ready(function($) {
        $('#module-card').toggleClass('w-100');
      })
    },
    onHide: function() {
      jQuery(document).ready(function($) {
        $('#module-card').toggleClass('w-100');
      })
    }
  }
]);
