jQuery(document).ready(function($) {
  var showAlert = true;
  var submitForm = true;

  // On click start timer and show quiz
  $("#start-quiz").click(function(e) {
    e.preventDefault();
    $("#timerbox").show();
    $.ajax({
      async: true,
      url: fundawande_ajax_object.ajaxurl,
      data: {
        action: "quiz_start"
      },
      dataType: "json",
      success: function() {
        // This outputs the result of the ajax request
        $("#start-quiz").hide();
        $("#quiz-form").show();
        // Run update of timer every second

        loadTimer(); // this will run after every second
      },
      error: function(errorThrown) {
        console.log(errorThrown);
      }
    }).done(function() {
      $("html, body").animate(
        {
          scrollTop: $("#sensei-quiz-list").offset().top
        },
        100
      );
    });
  });
  // Load the timer info ito the div so the user can keep track
  function loadTimer() {
    $.ajax({
      async: true,
      url: fundawande_ajax_object.ajaxurl,
      data: {
        action: "quiz_time"
      },
      dataType: "html",
      success: function(data) {
        //alert(data);
        // This outputs the result of the ajax request
        //console.log(data);
        function secondsTimeSpanToHMS(s) {
          var h = Math.floor(s / 3600); //Get whole hours
          s -= h * 3600;
          var m = Math.floor(s / 60); //Get remaining minutes
          s -= m * 60;
          return (
            h + ":" + (m < 10 ? "0" + m : m) + ":" + (s < 10 ? "0" + s : s)
          ); //zero padding on minutes and seconds
        }
        setInterval(function() {
          var time = secondsTimeSpanToHMS(data);

          // If more than 5 min remaining make green
          if (data > 300) {
            $("#timerbox").html(
              '<div id="time">Time Remaining: ' + time + "</div>"
            );
            $("#timerbox").addClass("green");
          }
          // If between 1 and 5 min make orange
          else if (data <= 300 && data > 60) {
            $("#timerbox").html(
              '<div id="time">Time Remaining: ' + time + "</div>"
            );
            $("#timerbox").addClass("orange");
          }

          // If less than 1 min make red
          else if (data > 0 && data <= 60) {
            $("#timerbox").html(
              '<div id="time">Time Remaining: ' + time + "</div>"
            );
            $("#timerbox").addClass("red");
          }

          // If 0 time complete the quiz and alert user
          else if (data == 0) {
            $("#timerbox").html(
              '<div id="time">Time Remaining: ' + time + "</div>"
            );
            if (showAlert == true) {
              alert(
                "Quiz time limit reached, your quiz will now be completed. Press 'Ok' and please wait while we submit your results..."
              );
              $('input[name="quiz_complete"]')
                .addClass("auto-complete")
                .trigger("click");
              showAlert = false;
            }
            $.ajax({
              url: fundawande_ajax_object.ajaxurl,
              data: {
                action: "quiz_end"
              },
              success: function(data2) {
                // This outputs the result of the ajax request
                $("#timerbox").html(data2);
              },
              error: function(errorThrown) {
                console.log(errorThrown);
              }
            });
          }

          // Make sure quiz is completed
          else if (data < 0) {
            $("#timerbox").html('<div id="time">Time Remaining: 0:00:00</div>');
            if (submitForm == true) {
              $('input[name="quiz_complete"]')
                .addClass("auto-complete")
                .trigger("click");
              submitForm = false;
            }
            $.ajax({
              url: fundawande_ajax_object.ajaxurl,
              data: {
                action: "quiz_end"
              },
              success: function(data2) {
                // This outputs the result of the ajax request
                $("#timerbox").html(data2);
              },
              error: function(errorThrown) {
                console.log(errorThrown);
              }
            });
          }
          data = data - 1;
        }, 1000);
      },
      error: function(errorThrown) {
        console.log(errorThrown);
      }
    });
  }

  // Process quiz resetting if reset quiz button is clicked
  $('input[name="quiz_reset"]').bind("mousedown touchstart click", function(e) {
    var button = $(this);

    // check button isn't disabled
    if (!button.hasClass("disabled")) {
      e.preventDefault();
      // Chech if the user really wants to reset quiz
      var c = confirm("Are you sure you want to reset the quiz?");
      if (!c) {
        return c; //you can just return c because it will be true or false
      }
      $("html, body").css("cursor", "progress");
      $(this)
        .addClass("disabled")
        .val("Resetting...");

      // submit the quiz form
      button.click();
    }

    // if disabled then let it submit as normal so sensei can do it's thing
  });

  // Process quiz completion if complete quiz button is clicked
  $('input[name="quiz_complete"]').bind("mousedown touchstart click", function(
    e
  ) {
    var button = $(this);

    // check button isn't disabled
    if (!button.hasClass("disabled")) {
      e.preventDefault();

      if (!button.hasClass("auto-complete")) {
        if (!confirm("Are you sure you want to complete the quiz?")) {
          return false;
        }
      }
      $("html, body").css("cursor", "progress");
      button.addClass("disabled").val("Submitting...");
      $.ajax({
        url: fundawande_ajax_object.ajaxurl,
        data: {
          action: "quiz_end"
        },
        success: function(data) {
          // This outputs the result of the ajax request
          $("#timerbox").html(data);
        },
        error: function(errorThrown) {
          console.log(errorThrown);
          button.removeClass("disabled");
        }
      });

      // submit the quiz form
      button.click();
    }

    // if disabled then let it submit as normal so sensei can do it's thing
  });
});
