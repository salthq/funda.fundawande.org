// English Lesssons Page Walkthrough

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
    target : '#sidebar-minimized',
    position: 'bottom',
    content : 'Click this button to view unit progress',
    buttons: [AnnoButton.NextButton]
  },
  {
    target : '#navigation-links',
    position: 'top',
    content : 'The navigation links help you to move between lessons within the course',
    buttons: [AnnoButton.DoneButton]
  }])