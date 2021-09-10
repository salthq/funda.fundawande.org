/* Get Our Elements */
const players = document.querySelectorAll(".player");
timeoutHandle = null;
players.forEach(function(player) {
  // Set all the video elements that pertain to a specific video, especially important if multiple videos exist on one page
  const video = player.querySelector(".viewer");
  const start = player.querySelector(".player__start");
  const end = player.querySelector(".player__end");
  const controls = player.querySelector(".player__controls");
  const progress = player.querySelector(".progress");
  const progressBar = player.querySelector(".progress__filled");
  const progressLoaded = player.querySelector(".progress__loaded");
  const toggle = player.querySelector(".toggle");
  const currentTimer = player.querySelector(".current-time");
  const durationTimer = player.querySelector(".duration-time");
  const sound = player.querySelector(".sound");
  const settingsButton = player.querySelector(".settings-toggle");
  const settings = player.querySelector(".settings");
  const skipButtons = player.querySelectorAll("[data-skip]");
  const ranges = player.querySelectorAll(".player__slider");
  const speeds = player.querySelectorAll(".speed");
  const fullscreen = player.querySelector(".fullscreen");
  const modal = player.closest(".modal");

  /* Build out functions */

  // Function to calculate the time in 00:00 format for printing out
  function fmtMSS(s) {
    return (s - (s %= 60)) / 60 + (9 < s ? ":" : ":0") + s;
  }

  // Set current time and video duration
  function onTrackedVideoFrame(currentTime, duration) {
    currentTimer.textContent = fmtMSS(Math.round(currentTime)); //Change #current to currentTime
    durationTimer.textContent = fmtMSS(Math.round(duration));
  }

  // Toggle the overall play/pause
  function togglePlay() {
    const method = video.paused ? "play" : "pause";

    if (method === "pause") {
      if (timeoutHandle) {
        clearTimeout(timeoutHandle);
      }
      controls.style.display = "flex";
    } else {
      startControlsTimer();
      controlsToggle();
    }

    video[method]();
  }

  // Handle replay click at the end of video
  function handleReplay() {
    end.style.display = "none";
    togglePlay();
  }

  // Handle the play/pause update button
  function updateButton() {
    const icon = this.paused ? "►" : "❚❚";
    toggle.textContent = icon;
  }

  // Handle skip functionality
  function skip() {
    video.currentTime += parseFloat(this.dataset.skip);
  }

  // Handle rnage update (currently only used for volume
  function handleRangeUpdate() {
    video[this.name] = this.value;
  }

  // Handle playback rate update
  function handlePlaybackUpdate() {
    video[this.name] = this.value;
    settingsToggle();
  }

  // Handle progress bar updates
  function handleProgress() {
    const percent = (video.currentTime / video.duration) * 100;
    const loaded = (video.currentTime / video.buffered.length) * 100;
    progressBar.style.flexBasis = percent + "%";
    progressBar.style.width = percent + "%";
    if (progressLoaded.style.flexBasis.replace("%", "") < 100) {
      progressLoaded.style.flexBasis = loaded + "%";
      progressLoaded.style.width = loaded + "%";
    }
    onTrackedVideoFrame(video.currentTime, video.duration);
  }

  // Handle mouse/finger scrubbing
  function scrub(e) {
    const scrubTime = (e.offsetX / progress.offsetWidth) * video.duration;
    const percent = (scrubTime / video.duration) * 100;
    progressBar.style.flexBasis = percent + "%";
    progressBar.style.width = percent + "%";
  }

  // Handle mouse/finger scrubbing
  function scrubEnd(e) {
    const scrubTime = (e.offsetX / progress.offsetWidth) * video.duration;
    video.currentTime = scrubTime;
  }

  // Handle muting
  function muteToggle() {
    if (video.muted) {
      video.muted = false;
      sound.classList.remove("muted");
    } else {
      video.muted = true;
      sound.classList.add("muted");
    }
  }

  // Handle showing or hiding settings block
  function settingsToggle() {
    if (settings.style.display === "block") {
      settings.style.display = "none";
    } else {
      settings.style.display = "block";
    }
  }
  function startControlsTimer() {
    timeoutHandle = setTimeout(function() {
      controls.style.display = "none";
    }, 3000); // <-- time in milliseconds
  }

  // Handle showing or hiding controls block
  function controlsToggle() {
    if (timeoutHandle && !video.paused) {
      controls.style.display = "flex";
      clearTimeout(timeoutHandle);
      startControlsTimer();
    }
  }

  // Handle video start screen click
  function playerStart() {
    start.style.display = "none";
    controls.style.display = "flex";
    toggle.focus();
    togglePlay();
  }

  // Handle video stop screen click
  function playerStop() {
    video["pause"]();

    if (timeoutHandle) {
      clearTimeout(timeoutHandle);
    }
    controls.style.display = "flex";
  }

  // Handle showing replay screen at end
  function playerEnd() {
    end.style.display = "flex";
  }

  // Handle full screen toggle functionality
  function toggleFullScreen() {
    var isInFullScreen =
      (document.fullscreenElement && document.fullscreenElement !== null) ||
      (document.webkitFullscreenElement &&
        document.webkitFullscreenElement !== null) ||
      (document.mozFullScreenElement &&
        document.mozFullScreenElement !== null) ||
      (document.msFullscreenElement && document.msFullscreenElement !== null);

    if (!isInFullScreen) {
      if (player.requestFullscreen) {
        player.requestFullscreen();
      } else if (player.mozRequestFullScreen) {
        docElm.mozRequestFullScreen();
      } else if (player.webkitRequestFullScreen) {
        player.webkitRequestFullScreen();
      } else if (player.msRequestFullscreen) {
        player.msRequestFullscreen();
      } else {
        alert("Fullscreen API is not supported");
        document.getElementById("btnFullScreen").disabled = true;
      }
    } else {
      if (document.exitFullscreen) {
        document.exitFullscreen();
      } else if (document.webkitExitFullscreen) {
        document.webkitExitFullscreen();
      } else if (document.mozCancelFullScreen) {
        document.mozCancelFullScreen();
      } else if (document.msExitFullscreen) {
        document.msExitFullscreen();
      }
    }
  }

  /* Hook up the event listeners */
  // Hook play pause on video click
  video.addEventListener("click", togglePlay);

  // Hook full screen on full screen click
  fullscreen.addEventListener("click", toggleFullScreen);

  // Hook play on start screen click
  start.addEventListener("click", playerStart);

  // Hook speed changes for speed click
  speeds.forEach(function(speed) {
    return speed.addEventListener("click", handlePlaybackUpdate);
  });

  // Hook button updates on play/pause
  video.addEventListener("play", updateButton);
  video.addEventListener("pause", updateButton);

  if (modal) {
    jQuery(document).ready(function($) {
      $(document).ready(function() {
        $(modal).on("show.bs.modal", function() {
          if (!$(".player__mobile").length) {
            playerStart();
          }
        });

        $(modal).on("hide.bs.modal", function() {
          playerStop();
        });
      });
    });
  }

  // Hook progress bar change on time update
  video.addEventListener("timeupdate", handleProgress);

  // Hook play on play button click
  toggle.addEventListener("click", togglePlay);

  // Hook replay on replay screen click
  end.addEventListener("click", handleReplay);

  // Hook skip on skip back button click
  skipButtons.forEach(function(button) {
    return button.addEventListener("click", skip);
  });

  // Hook range volume update on change
  ranges.forEach(function(range) {
    return range.addEventListener("change", handleRangeUpdate);
  });

  // Hook change for mousemove on the range volume
  ranges.forEach(function(range) {
    return range.addEventListener("mousemove", handleRangeUpdate);
  });

  var mousedown = false;

  progress.addEventListener("mousemove", function(e) {
    return mousedown && scrub(e);
  });
  progress.addEventListener("mousedown", function(e) {
    scrubEnd(e);
    return (mousedown = true);
  });
  progress.addEventListener("mouseup", function(e) {
    scrubEnd(e);
    return (mousedown = false);
  });

  // Hook show controls on mouse move
  player.addEventListener("mousemove", controlsToggle);

  // Hook progress scrub change
  progress.addEventListener("click", scrub);

  // Hook mute on mute button click
  sound.addEventListener("click", muteToggle);

  // Hook showing settings on settings button click
  settingsButton.addEventListener("click", settingsToggle);

  // Hook progress change on loaded meta to show initial times (duration)
  video.addEventListener("loadedmetadata", function() {
    handleProgress();
  });

  // Hook replay screen to show on video end
  video.addEventListener("ended", function() {
    playerEnd();
  });

  // TODO: Add hook to show lesson next button on last video at >75%
});
