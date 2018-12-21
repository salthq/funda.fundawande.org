//Xhosa Units Page Walkthrough
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
  target : '#back-to-modules',
  position: 'right',
  content : 'Cofa le qhosha ukubuyela kuluhlu lwee modyuli',
  buttons: [AnnoButton.BackButton,AnnoButton.NextButton]

},
// //TODO: uncomment the view lessons tooltip below {
//   target: '#view-lessons',
//   position: 'left',
//   content: 'Cofa le iqhosha ukujonga izifundo ngaphakathi kwunithi',
//   buttons: [AnnoButton.BackButton,AnnoButton.NextButton]
// }, 
{
  target: '#resume-unit',
  position: 'left',
  content: 'Cofa apha ukuze ufinyelele isifundo sakho samanje ngaphakathi kwunithi',
  buttons: [AnnoButton.BackButton,AnnoButton.DoneButton]
}])