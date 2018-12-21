//Xhosa Module Page Walkthrough
var tour = new Anno([{
  target: '#wrapper-navbar',
  position: 'right',
  content: "Cofa iqhosha 'Vula Imenyu' ukuze ubone iinketho ezahlukeneyo zemenyu.",
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
  content : "La makhadi abonisa ulwazi malunga nondyulo nganye. Cofa kwi 'jonga imodyuli' ukubona iiyunithi kule modyuli",
  buttons: [AnnoButton.BackButton, AnnoButton.DoneButton]
}])