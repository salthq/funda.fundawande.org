//Xhosa Units Page Walkthrough

var tour = new Anno([{
  target: '#wrapper-navbar',
  position: 'bottom',
  content: "Cofa iqhosha 'Vula Imenyu' ukuze ubone iinketho ezahlukeneyo zemenyu.",
  buttons: [AnnoButton.NextButton]
},
{
  target: '#view-tooltips',
  position: 'bottom',
  content: "Inkinobho ethi 'View Tooltips' iya kuphinda ilayishe le ndlela.",
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
  content: 'Cofa le qhosha ukubuyela kuluhlu lwee modyuli',
  buttons: [AnnoButton.BackButton,AnnoButton.NextButton]
}, 
{
  target: '#resume-unit',
  position: 'left',
  content: 'Cofa apha ukuze ufinyelele isifundo sakho samanje ngaphakathi kwunithi',
  buttons: [AnnoButton.BackButton,AnnoButton.DoneButton]
}])