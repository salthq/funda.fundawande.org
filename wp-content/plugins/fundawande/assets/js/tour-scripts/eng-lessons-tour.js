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
    buttons: [AnnoButton.NextButton]
  },
  {
    target: '#back-to-units',
    position: 'right',
    content: 'Click this button to go back to the units view',
    buttons: [AnnoButton.BackButton, AnnoButton.NextButton]
  },
  {
    target : '#sidebar-minimized',
    position: 'right',
    content : 'Click this button to view unit progress',
    buttons: [AnnoButton.BackButton, AnnoButton.NextButton]
  },
  {
    target : '#navigation-links',
    position: 'top',
    content : 'The navigation links help you to move between lessons within the course',
    buttons: [AnnoButton.DoneButton]
  }])