//English Module Page Walkthrough

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
    target : '#module-card',
    position: 'bottom',
    content : "These cards show you information about each module. Click on 'view module' to see the units within that module",
    buttons: [AnnoButton.BackButton, AnnoButton.DoneButton]
  }])