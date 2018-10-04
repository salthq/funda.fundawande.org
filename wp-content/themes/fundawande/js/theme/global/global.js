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
