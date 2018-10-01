// Initial Site Launch Page Load
var initial_tour = new Anno([{
    target: '#view-tooltips',
    position: 'bottom',
    content: "To see a walkthrough of the features for the page you are on, click the 'View Tooltips' button",
    buttons: [AnnoButton.DoneButton]
  }])

  //Show the tour as soon as the script loads.
  initial_tour.show();