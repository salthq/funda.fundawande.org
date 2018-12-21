// English Lesssons Page Walkthrough

var tour = new Anno([{
    target: '#wrapper-navbar',
    position: 'right',
    content: "Click 'Open Menu' to see the different menu options.",
    onShow: function() {
      jQuery(document).ready( function($) {
        $('#main-menu-modal').modal('show');
      });
    },  
    onHide: function() {
      jQuery(document).ready( function($) {
        $('#main-menu-modal').modal('hide');
      });
    },
    position: {
      top: '8em',
      left: '23em'
    },
    uttons: [
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
    target: '#back-to-units',
    position: 'right',
    content: 'Click this button to go back to the units view',
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
  {
    target : '#sidebar-minimized',
    position: 'right',
    content : 'Your progress is shown here. For more detail, click on the sidebar',
    buttons: [{
	    text: 'Back',
	    className: 'anno-btn-low-importance',
	    click: function() {
	      return this.switchToChainPrev();
      }
    }, 
    {
      text: 'Next',
      className: 'pulse',
      click: function() {
        jQuery(document).ready(function($) {
          $('#sidebar-minimized').css('display', 'none');
          $('#sidebar-expanded').css('margin-left', '0px');
        })
        return this.switchToChainNext();
      }
    }]
  },
  {
    target: '#sidebar-expanded',
    position: 'top',
    content: 'You can now see an expanded view of the lessons within this unit. You can navigate to different lessons by clicking on their icon in this list',
    buttons: [{
	    text: 'Back',
	    className: 'anno-btn-low-importance',
	    click: function() {
	      return this.switchToChainPrev();
      }
    }, 
    {
      text: 'Next',
      className: 'pulse',
      click: function() {
        jQuery(document).ready(function($) {
          $('#sidebar-minimized').css('display', 'block');
          $('#sidebar-expanded').css('margin-left', '-500px');
        })
        return this.switchToChainNext();
      }
    }]
  },
  {
    target : '#navigation-links',
    position: 'top',
    content : 'These navigation links help you to move between lessons within the course',
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
  }])