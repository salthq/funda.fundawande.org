<?php
/**
 * Timber functionality to the boilerplate
 *
 * Author: Pango
 */


if ( ! class_exists( 'Timber' ) ) {
    add_action( 'admin_notices', function() {
        echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php') ) . '</a></p></div>';
    });

    return;
}

Timber::$dirname = array('templates', 'views');

class StarterSite extends TimberSite {

    function __construct() {
        add_theme_support( 'post-formats' );
        add_theme_support( 'post-thumbnails' );
        add_theme_support( 'menus' );
        add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );
        add_filter( 'timber_context', array( $this, 'add_to_context' ) );
        add_filter( 'get_twig', array( $this, 'add_to_twig' ) );
        parent::__construct();
    }


    function add_to_context( $context ) {


        $context['current_user'] = new Timber\User();

        if (isset($_GET['login'])) {
         if ( $_GET['login'] == 'failed') {
             $context['error_message'] = "incorrect_credentials";
         }
         else if ( $_GET['login'] == 'blank') {
             $context['error_message'] = "blank_field";
         }
        }
        $context['user_logged_in'] = is_user_logged_in();
        $context['log_out_link'] = wp_logout_url();
        $context['foo'] = 'bar';
        $context['stuff'] = 'I am a value set in your functions.php file';
        $context['notes'] = 'These values are available everytime you call Timber::get_context();';
        $context['menu'] = new TimberMenu();
        $context['foo'] = 'bar';
        $context['stuff'] = 'I am a value set in your functions.php file';
        $context['notes'] = 'These values are available everytime you call Timber::get_context();';
        $context['menu'] = new TimberMenu();
        $context['learner_menu'] = new Timber\Menu('learner-menu');
        $context['coach_menu'] = new Timber\Menu('coach-menu');
        $context['site'] = $this;
        $context['is_mobile'] = wp_is_mobile();
        // Set up language context to determine page language
        if ( isset($context['current_user']->language_preference)) {
            $context['lang'] = FundaWande()->language->get_language($context['current_user']->language_preference);
        }
        else {
            $context['lang'] = FundaWande()->language->get_language(null);
        }

        return $context;
    }

    function myfoo( $text ) {
        $text .= ' bar!';
        return $text;
    }

    function add_to_twig( $twig ) {
        /* this is where you can add your own functions to twig */
        $twig->addExtension( new Twig_Extension_StringLoader() );
        $twig->addFilter('myfoo', new Twig_SimpleFilter('myfoo', array($this, 'myfoo')));
        return $twig;
    }

}

new StarterSite();