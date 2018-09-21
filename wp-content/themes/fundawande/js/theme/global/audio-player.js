/* Get Our Elements */
const audio_players = document.querySelectorAll('.audio_player');
audio_players.forEach(function (player) {
    // Set all the video elements that pertain to a specific video, especially important if multiple videos exist on one page
    const video = player.querySelector('.viewer');
    const start = player.querySelector('.player__start');
    const end = player.querySelector('.player__end');
    const controls = player.querySelector('.player__controls');
    const progress = player.querySelector('.progress');
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


});