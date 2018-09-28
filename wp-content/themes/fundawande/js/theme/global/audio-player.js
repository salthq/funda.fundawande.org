/* Get Our Elements */
const audio_players = document.querySelectorAll('.audio_player_container');
audio_players.forEach(function (player) {
    // Set all the video elements that pertain to a specific video, especially important if multiple videos exist on one page
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