//English Units Page Walkthrough
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
    target: "#back-to-modules",
    position: "right",
    content: "Click this button to go back to the list of modules",
    buttons: [
      {
        text: "Back",
        className: "anno-btn-low-importance",
        click: function() {
          return this.switchToChainPrev();
        }
      },
      {
        text: "Next",
        className: "pulse",
        click: function() {
          return this.switchToChainNext();
        }
      }
    ],
    onShow: function() {
      jQuery(document).ready( function($) {
        $('html, body').animate({ scrollTop: 0 }, 100, function () {});
      })
    }
  },
  // {
  //   target: "#view-lessons",
  //   position: "left",
  //   content: "Click this button to view the lessons within the unit",
  //   buttons: [{
	//     text: 'Back',
	//     className: 'anno-btn-low-importance',
	//     click: function() {
	//       return this.switchToChainPrev();
  //     }
  //   }, 
  //   {
  //     text: 'Next',
  //     className: 'pulse',
  //     click: function() {
  //       jQuery(document).ready(function($) {
  //         $('#view-lessons').toggleClass('collapsed');
  //         $('#collapse-unit1').toggleClass('collapse show');
  //       })
  //       return this.switchToChainNext();
  //     }
  //   }]
  // },
  {
    target: '#collapse-unit1',
    position: "top",
    content: "This is the list of lessons within the unit. Click on a lesson icon to access that lesson",
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
    ]
  }

  // {
  //   target: "#resume-unit",
  //   position: "left",
  //   content: "Click here to access your current lesson within the unit",
  //   buttons: [AnnoButton.BackButton, AnnoButton.DoneButton]
  // }
]);
