var tour = new Anno([{
    target: '#wrapper-navbar',
    position: 'bottom',
    content: 'This is the navbar',
    buttons: [AnnoButton.NextButton]
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
  }])