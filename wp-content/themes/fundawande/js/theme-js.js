/* Get Our Elements */
const audio_players = document.querySelectorAll('.audio_player_container');
audio_players.forEach(function (player) {
    // Set all the audio elements that pertain to a specific audio clip, especially important if multiple audio clips exist on one page
    var audio = player.querySelector('.audio'); // id for audio element
    var duration = audio.duration; // Duration of audio clip, calculated here for embedding purposes
    var closeButton = player.querySelector('.close__button'); // play button
    var toggle = player.querySelector('.play__button'); // play button
    var playhead = player.querySelector('.play_head'); // playhead
    var progress = player.querySelector('.progress'); // timeline
    const progressBar = player.querySelector('.progress__filled');
    const currentTimer = player.querySelector('.current-time');
    const durationTimer = player.querySelector('.duration-time');
    const openButton = player.querySelector('.open-toggle');
    const playerBlock = player.querySelector('.audio_player');

// timeline width adjusted for playhead
//     var timelineWidth = progress.offsetWidth - playhead.offsetWidth;

    // Function to calculate the time in 00:00 format for printing out
    function fmtMSS(s){
        return(s-(s%=60))/60+(9<s?':':':0')+s
    }

    // Handle mouse/finger scrubbing
    function scrub(e) {
        const scrubTime = (e.offsetX / progress.offsetWidth) * audio.duration;
        audio.currentTime = scrubTime;
    }


    // Toggle the overall play/pause
    function togglePlay() {
        const method = audio.paused ? 'play' : 'pause';
        audio[method]();
    }

// timeupdate event listener
    audio.addEventListener("timeupdate", handleProgress, false);



// returns click as decimal (.77) of the total timelineWidth
//     function clickPercent(event) {
//         return (event.clientX - getPosition(progress)) / timelineWidth;
//     }

// makes playhead draggable
//     playhead.addEventListener('mousedown', mouseDown, false);
    window.addEventListener('mouseup', mouseUp, false);

// Boolean value so that audio position is updated only when the playhead is released
    var onplayhead = false;

// mouseDown EventListener
    function mouseDown() {
        onplayhead = true;
        window.addEventListener('mousemove', moveplayhead, true);
        audio.removeEventListener('timeupdate', handleProgress, false);
    }

// mouseUp EventListener
// getting input from all mouse clicks
    function mouseUp(event) {
        if (onplayhead == true) {
            moveplayhead(event);
            window.removeEventListener('mousemove', moveplayhead, true);
            // change current time
            audio.currentTime = duration * clickPercent(event);
            audio.addEventListener('timeupdate', handleProgress, false);
        }
        onplayhead = false;
    }

    // Handle the play/pause update button
    function updateButton() {
        const icon = this.paused ? '►' : '❚❚';
        toggle.textContent = icon;
    }

    // Set current time and video duration
    function onTrackedAudioFrame(currentTime, duration){
        currentTimer.textContent = fmtMSS(Math.round(currentTime)); //Change #current to currentTime
        durationTimer.textContent = fmtMSS(Math.round(duration));
    }

    // Handle progress bar updates
    function handleProgress() {
        const percent = (audio.currentTime / audio.duration) * 100;
        progressBar.style.flexBasis = percent + "%";
        progressBar.style.width = percent + "%";
        onTrackedAudioFrame(audio.currentTime, audio.duration);
    }

    // Handle showing or hiding audio block
    function openToggle() {
        if (playerBlock.style.display === "flex") {
            playerBlock.style.display = "none"
            audio['pause']();
        } else {
            playerBlock.style.display = "flex"
        }
    }

    // Hook button updates on play/pause
    audio.addEventListener('play', updateButton);
    audio.addEventListener('pause', updateButton);

    // Hook play on play button click
    toggle.addEventListener('click', togglePlay);

    // Gets audio file duration
    audio.addEventListener("canplaythrough", function() {
        duration = audio.duration;
    }, false);

    var mousedown = false;
    progress.addEventListener('mousemove', function (e) {
        return mousedown && scrub(e);
    });
    progress.addEventListener('mousedown', function () {
        return mousedown = true;
    });
    progress.addEventListener('mouseup', function () {
        return mousedown = false;
    });

    // Hook progress scrub change
    progress.addEventListener('click', scrub);

    // Hook progress change on loaded meta to show initial times (duration)
    audio.addEventListener("loadedmetadata", function() {
        handleProgress();
    });

    // Hook open toggle on open click
    openButton.addEventListener('click', openToggle);

    // Hook close on close click
    closeButton.addEventListener('click', openToggle);


// getPosition
// Returns elements left position relative to top-left of viewport
    function getPosition(el) {
        return el.getBoundingClientRect().left;
    }




});
jQuery(document).ready( function($) {
    $('#main-menu-modal').on('shown.bs.modal', function (e) {
        $('#wrapper-navbar').addClass('main-menu-active');
    })

    $('#main-menu-modal').on('hidden.bs.modal', function (e) {
        $('#wrapper-navbar').removeClass('main-menu-active');
        $('.nav-hamburger').removeClass('is-active');
    })

    $('.fw-get-help').on('click', function (e) {
        e.preventDefault();
        Intercom('update', {
            "hide_default_launcher": false
        });
        Intercom('show');

    })

});

/**
 * Created by jamestrevorlees on 2018/10/01.
 * Created to house the google tag manager container
 */

<!-- Google Tag Manager -->
(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-P56FTZ9');
<!-- End Google Tag Manager -->



(function() {


    const mainmenu = document.getElementById("main-menu-button");

    if (mainmenu) {
        mainmenu.addEventListener( "click", function(e) {
            e.preventDefault();
            const toggle = this.querySelector(".nav-hamburger");
            (toggle.classList.contains("is-active") === true) ? toggle.classList.remove("is-active") : toggle.classList.add("is-active");
        });
    }

})();
/* Get Our Elements */
const players = document.querySelectorAll('.player');
timeoutHandle = null;
players.forEach(function (player) {
    // Set all the video elements that pertain to a specific video, especially important if multiple videos exist on one page
    const video = player.querySelector('.viewer');
    const start = player.querySelector('.player__start');
    const end = player.querySelector('.player__end');
    const controls = player.querySelector('.player__controls');
    const progress = player.querySelector('.progress-container');
    const progressBar = player.querySelector('.progress__filled');
    const progressLoaded = player.querySelector('.progress__loaded');
    const toggle = player.querySelector('.toggle');
    const currentTimer = player.querySelector('.current-time');
    const durationTimer = player.querySelector('.duration-time');
    const sound = player.querySelector('.sound');
    const settingsButton = player.querySelector('.settings-toggle');
    const settings = player.querySelector('.settings');
    const skipButtons = player.querySelectorAll('[data-skip]');
    const ranges = player.querySelectorAll('.player__slider');
    const speeds = player.querySelectorAll('.speed');
    const fullscreen = player.querySelector('.fullscreen');
    const modal = player.closest(".modal");

    /* Build out functions */

    // Function to calculate the time in 00:00 format for printing out
    function fmtMSS(s){
        return(s-(s%=60))/60+(9<s?':':':0')+s
    }

    // Set current time and video duration
    function onTrackedVideoFrame(currentTime, duration){
        currentTimer.textContent = fmtMSS(Math.round(currentTime)); //Change #current to currentTime
        durationTimer.textContent = fmtMSS(Math.round(duration));
    }

    // Toggle the overall play/pause
    function togglePlay() {
        const method = video.paused ? 'play' : 'pause';

        if (method === 'pause') {

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
        const icon = this.paused ? '►' : '❚❚';
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
        if (progressLoaded.style.flexBasis.replace('%','') < 100) {
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
        if(video.muted) {
            video.muted = false;
            sound.classList.remove('muted');

        }
        else {
            video.muted = true;
            sound.classList.add('muted');

        }
    }

    // Handle showing or hiding settings block
    function settingsToggle() {
        if (settings.style.display === "block") {
            settings.style.display = "none"
        } else {
            settings.style.display = "block"
        }
    }
    function startControlsTimer() {
        timeoutHandle = setTimeout(function() {
            controls.style.display = "none"
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
        video['pause']();

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
        var isInFullScreen = (document.fullscreenElement && document.fullscreenElement !== null) ||
        (document.webkitFullscreenElement && document.webkitFullscreenElement !== null) ||
        (document.mozFullScreenElement && document.mozFullScreenElement !== null) ||
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
            }
            else {
                alert("Fullscreen API is not supported");
                document.getElementById('btnFullScreen').disabled = true;
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
    video.addEventListener('click', togglePlay);

    // Hook full screen on full screen click
    fullscreen.addEventListener('click', toggleFullScreen);

    // Hook play on start screen click
    start.addEventListener('click', playerStart);

    // Hook speed changes for speed click
    speeds.forEach(function (speed) {
        return speed.addEventListener('click', handlePlaybackUpdate);
    });

    // Hook button updates on play/pause
    video.addEventListener('play', updateButton);
    video.addEventListener('pause', updateButton);



    if (modal) {
        jQuery(document).ready(function ($) {
            $(document).ready(function () {
                $(modal).on('show.bs.modal', function () {
                    playerStart();
                });

                $(modal).on('hide.bs.modal', function () {
                    playerStop();
                });
            });
        });
    }


    // Hook progress bar change on time update
    video.addEventListener('timeupdate', handleProgress);

    // Hook play on play button click
    toggle.addEventListener('click', togglePlay);

    // Hook replay on replay screen click
    end.addEventListener('click', handleReplay);

    // Hook skip on skip back button click
    skipButtons.forEach(function (button) {
        return button.addEventListener('click', skip);
    });

    // Hook range volume update on change
    ranges.forEach(function (range) {
        return range.addEventListener('change', handleRangeUpdate);
    });

    // Hook change for mousemove on the range volume
    ranges.forEach(function (range) {
        return range.addEventListener('mousemove', handleRangeUpdate);
    });

    var mousedown = false;

    progress.addEventListener('mousemove', function (e) {
        return mousedown && scrub(e);
    });
    progress.addEventListener('mousedown', function (e) {
        scrubEnd(e);
        return mousedown = true;
    });
    progress.addEventListener('mouseup', function (e) {
        scrubEnd(e);
        return mousedown = false;
    });

    // Hook show controls on mouse move
    player.addEventListener('mousemove', controlsToggle);

    // Hook progress scrub change
    progress.addEventListener('click', scrub);

    // Hook mute on mute button click
    sound.addEventListener('click', muteToggle);

    // Hook showing settings on settings button click
    settingsButton.addEventListener('click', settingsToggle);

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