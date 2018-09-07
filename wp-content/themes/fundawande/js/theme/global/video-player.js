/* Get Our Elements */
const player = document.querySelector('.player');
const video = player.querySelector('.viewer');
const start = player.querySelector('.player__start');
const progress = player.querySelector('.progress');
const progressBar = player.querySelector('.progress__filled');
const toggle = player.querySelector('.toggle');
const currentTimer = player.querySelector('.current-time');
const durationTimer = player.querySelector('.duration-time');
const sound = player.querySelector('.sound');
const settingsButton = player.querySelector('.settings-toggle');
const settings = player.querySelector('.settings');
const skipButtons = player.querySelectorAll('[data-skip]');
const ranges = player.querySelectorAll('.player__slider');
const speeds = player.querySelectorAll('.speed');

function fmtMSS(s){

    return(s-(s%=60))/60+(9<s?':':':0')+s
}


function onTrackedVideoFrame(currentTime, duration){
    currentTimer.textContent = fmtMSS(Math.round(currentTime)); //Change #current to currentTime
    durationTimer.textContent = fmtMSS(Math.round(duration));
}


/* Build out functions */

function togglePlay() {
    const method = video.paused ? 'play' : 'pause';
    video[method]();
}

function updateButton() {
    const icon = this.paused ? '►' : '❚ ❚';
    console.log(icon);
    toggle.textContent = icon;
}

function skip() {
    video.currentTime += parseFloat(this.dataset.skip);
}

function handleRangeUpdate() {
    video[this.name] = this.value;
}

function handleProgress() {
    const percent = (video.currentTime / video.duration) * 100;
    progressBar.style.flexBasis = percent + "%";
    onTrackedVideoFrame(video.currentTime, video.duration);

}

function scrub(e) {
    const scrubTime = (e.offsetX / progress.offsetWidth) * video.duration;
    video.currentTime = scrubTime;
}

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

function settingsToggle() {
    if (settings.style.display === "block") {
        settings.style.display = "none"
    } else {
        settings.style.display = "block"
    }
}

function playerStart() {
    start.style.display = "none";
    togglePlay();

}

/* Hook up the event listeners */
video.addEventListener('click', togglePlay);
start.addEventListener('click', playerStart);
speeds.forEach(function (speed) {
    return speed.addEventListener('click', handleRangeUpdate);
});
video.addEventListener('play', updateButton);
video.addEventListener('pause', updateButton);
video.addEventListener('timeupdate', handleProgress);

toggle.addEventListener('click', togglePlay);
skipButtons.forEach(function (button) {
    return button.addEventListener('click', skip);
});

ranges.forEach(function (range) {
    return range.addEventListener('change', handleRangeUpdate);
});
ranges.forEach(function (range) {
    return range.addEventListener('mousemove', handleRangeUpdate);
});

var mousedown = false;
progress.addEventListener('click', scrub);
sound.addEventListener('click', muteToggle);
settingsButton.addEventListener('click', settingsToggle);
progress.addEventListener('mousemove', function (e) {
    return mousedown && scrub(e);
});
progress.addEventListener('mousedown', function () {
    return mousedown = true;
});
progress.addEventListener('mouseup', function () {
    return mousedown = false;
});


function toggleFullScreen() {
    if (player.requestFullscreen) {
        if (document.fullScreenElement) {
            document.cancelFullScreen();
        } else {
            player.requestFullscreen();
        }
    }
    else if (player.msRequestFullscreen) {
        if (document.msFullscreenElement) {
            document.msExitFullscreen();
        } else {
            player.msRequestFullscreen();
        }
    }
    else if (player.mozRequestFullScreen) {
        if (document.mozFullScreenElement) {
            document.mozCancelFullScreen();
        } else {
            player.mozRequestFullScreen();
        }
    }
    else if (player.webkitRequestFullscreen) {
        if (document.webkitFullscreenElement) {
            document.webkitCancelFullScreen();
        } else {
            player.webkitRequestFullscreen();
        }
    }
    else {
        alert("Fullscreen API is not supported");
        document.getElementById('btnFullScreen').disabled = true;
    }
}

video.addEventListener("loadedmetadata", function() {
    handleProgress();
});