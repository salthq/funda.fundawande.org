//English Module Page Walkthrough

var tour = new Anno([{
    target: '#wrapper-navbar',
    position: 'bottom',
    content: "Click 'Open Menu' to see the different menu options.",
    buttons: [AnnoButton.NextButton]
  },
  {
    target : '#module-card',
    position: 'bottom',
    content : "These cards show you information about each module. Click on 'view module' to see the units within that module",
    buttons: [AnnoButton.BackButton, AnnoButton.DoneButton]
  }])