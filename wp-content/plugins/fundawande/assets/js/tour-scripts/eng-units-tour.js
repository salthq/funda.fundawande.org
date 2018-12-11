//English Units Page Walkthrough

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
      top: '3em',
      left: '15em'
    },
    buttons: [AnnoButton.NextButton]
  },
  {
    target : '#back-to-modules',
    position: 'right',
    content : 'Click this button to go back to the list of modules',
    buttons: [AnnoButton.BackButton,AnnoButton.NextButton]

  },
  {
    target: '#view-lessons',
    position: 'left',
    content: 'Click this button to view the lessons within the unit',
    buttons: [AnnoButton.BackButton,AnnoButton.NextButton]
  }, 
  {
    target: '#resume-unit',
    position: 'left',
    content: 'Click here to access your current lesson within the unit',
    buttons: [AnnoButton.BackButton,AnnoButton.DoneButton]
  }])