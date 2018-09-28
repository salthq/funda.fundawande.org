//English Units Page Walkthrough

var tour = new Anno([{
    target: '#wrapper-navbar',
    position: 'bottom',
    content: "Click 'Open Menu' to see the different menu options.",
    buttons: [AnnoButton.NextButton]
  },
  {
    target: '#view-tooltips',
    position: 'bottom',
    content: "The 'View Tooltips' button will replay this walkthrough.",
    buttons: [AnnoButton.BackButton, AnnoButton.NextButton]
  },
  {
    target : '#back-to-modules',
    position: 'right',
    content : 'Click this button to go back to the list of modules',
    buttons: [AnnoButton.BackButton,AnnoButton.NextButton]

  }, {
    target: '#view-lessons',
    position: 'top',
    content: 'Click this button to view the lessons within the unit',
    buttons: [AnnoButton.BackButton,AnnoButton.NextButton]
  }, 
  {
    target: '#resume-unit',
    position: 'left',
    content: 'Click here to access your current lesson within the unit',
    buttons: [AnnoButton.BackButton,AnnoButton.DoneButton]
  }])