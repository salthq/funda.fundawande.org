(function() {


    const mainmenu = document.getElementById("main-menu-button");

    mainmenu.addEventListener( "click", function(e) {
        e.preventDefault();
        const toggle = this.querySelector(".nav-hamburger");
        (toggle.classList.contains("is-active") === true) ? toggle.classList.remove("is-active") : toggle.classList.add("is-active");
    });

})();