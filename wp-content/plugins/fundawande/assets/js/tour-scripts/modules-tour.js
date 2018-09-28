//Module Page Walkthrough

var tour = new Anno([{
    target: '#wrapper-navbar',
    position: 'bottom',
    content: 'This is the navbar',
    buttons: [AnnoButton.NextButton]
  },
  {
    target : '#module-card',
    position: 'top',
    content : 'These cards show you information about each module.',
    buttons: [AnnoButton.NextButton]
  }])