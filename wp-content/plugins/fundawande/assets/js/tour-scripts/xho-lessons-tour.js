// Xhosa Lesssons Page Walkthrough

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
  target : '#sidebar-minimized',
  position: 'bottom',
  content : 'Cofa le iqhosha ukujonga inkqubela phambili',
  buttons: [AnnoButton.NextButton]
},
{
  target : '#navigation-links',
  position: 'top',
  content : 'Izixhumanisi zokuhamba zikunceda ukuba uhambe phakathi kwezifundo ngaphakathi kwikhosi',
  buttons: [AnnoButton.DoneButton]
}])