// Coach Dashboard Tour
var tour = new Anno([{
    target: '#view-tooltips',
    position: 'bottom',
    content: "Welcome to the coach assessment review dashboard. From here you can manage all assessments which need feedback.",
    buttons: [
      {
        text: "Next",
        className: "pulse",
        click: function() {
          return this.switchToChainNext();
        }
      }
    ],  },
  {
    target: '#progress-dash-btn',
    position: 'bottom',
    content: "Click here to view learner progress information",
    buttons: [
      {
        text: "Back",
        className: "anno-btn-low-importance",
        click: function() {
          return this.switchToChainPrev();
        }
      },
      {
        text: "Next",
        className: "pulse",
        click: function() {
          return this.switchToChainNext();
        }
      }
    ],
  },
  {
    target: '#coach-dash-filters',
    position: 'bottom',
    content: "Select filters to view the course information",
    buttons: [
      {
        text: "Back",
        className: "anno-btn-low-importance",
        click: function() {
          return this.switchToChainPrev();
        }
      },
      {
        text: "Next",
        className: "pulse",
        click: function() {
          return this.switchToChainNext();
        }
      }
    ],
  },
  {
    target: '#courseSelect',
    position: 'right',
    content: "To get started, select a course",
    buttons: [
      {
        text: "Back",
        className: "anno-btn-low-importance",
        click: function() {
          return this.switchToChainPrev();
        }
      },
      {
        text: "Next",
        className: "pulse",
        click: function() {
          return this.switchToChainNext();
        }
      }
    ],
  },
  {
    target: '#coach-dash-filters-submit',
    position: 'right',
    content: "And click here to view the data table"
  }
])