//Xhosa Module Page Walkthrough

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
  target : '#module-card',
  position: 'bottom',
  content : "La makhadi abonisa ulwazi malunga nondyulo nganye. Cofa kwi 'jonga imodyuli' ukubona iiyunithi kule modyuli",
  buttons: [AnnoButton.BackButton, AnnoButton.DoneButton]
}])