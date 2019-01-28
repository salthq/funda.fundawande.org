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
  buttons: [AnnoButton.BackButton,AnnoButton.NextButton],
  onShow: function() {
    jQuery(document).ready( function($) {
      $('html, body').animate({ scrollTop: 0 }, 100, function () {});
    })
  }

},
// {
//   target: '#view-lessons',
//   position: 'left',
//   content: 'Cofa le iqhosha ukujonga izifundo ngaphakathi kwunithi',
//   buttons: [{
//     text: 'Back',
//     className: 'anno-btn-low-importance',
//     click: function() {
//       return this.switchToChainPrev();
//     }
//   }, 
//   {
//     text: 'Next',
//     className: 'pulse',
//     click: function() {
//       jQuery(document).ready(function($) {
//         $('#view-lessons').toggleClass('collapsed');
//         $('#collapse-unit1').toggleClass('collapse show');
//       })
//       return this.switchToChainNext();
//     }
//   }]
// },
{
  target: '#collapse-unit1',
  position: "top",
  content: "Olu uluhlu lwezifundo ngaphakathi kwunithi. Cofa kwisithonjana sokufunda ukufikelela kwisifundo",
  buttons: [{
    text: 'Buyela',
    className: 'anno-btn-low-importance',
    click: function() {
      return this.switchToChainPrev();
    }
  }, 
  {
    text: 'Yenziwe',
    className: 'pulse',
    click: function() {
      return this.hide();
    }
  }]
}
// {
//   target: '#resume-unit',
//   position: 'left',
//   content: 'Cofa apha ukuze ufinyelele isifundo sakho samanje ngaphakathi kwunithi',
//   buttons: [AnnoButton.BackButton,AnnoButton.DoneButton]
// }
])