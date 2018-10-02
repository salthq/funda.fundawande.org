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

// Add General Options Page
if (function_exists('acf_add_options_page')) {
    acf_add_options_page(array(
        'page_title'    => 'Funda Wande Global Options',
        'menu_title'    => 'FW Options',
        'menu_slug'    => 'fundawande-options',
        'capability'    => 'edit_posts',
        'redirect'        => false
    ));

    // Add Course Options Page
    acf_add_options_sub_page(array(
        'page_title' 	=> 'Funda Wande Course Options',
        'menu_title' 	=> 'Course Options',
        'parent_slug' 	=> 'fundawande-options',
    ));

    // Add Module Options Page
    acf_add_options_sub_page(array(
        'page_title' 	=> 'Funda Wande Module Options',
        'menu_title' 	=> 'Module Options',
        'parent_slug' 	=> 'fundawande-options',
    ));

    // Add Lesson Options Page
    acf_add_options_sub_page(array(
        'page_title' 	=> 'Funda Wande Lesson Options',
        'menu_title' 	=> 'Lesson Options',
        'parent_slug' 	=> 'fundawande-options',
    ));

    // Add Terms And Conditions Page
    acf_add_options_sub_page(array(
        'page_title' 	=> 'Funda Wande Terms and Conditions',
        'menu_title' 	=> 'Terms and Conditions Options',
        'parent_slug' 	=> 'fundawande-options',
    ));

    // Add Navbar Options Page
    acf_add_options_sub_page(array(
        'page_title' 	=> 'Funda Wande Navbar Options',
        'menu_title' 	=> 'Navbar Options',
        'parent_slug' 	=> 'fundawande-options',
    ));

    // Add 404 Options Page
    acf_add_options_sub_page(array(
        'page_title' 	=> 'Funda Wande 404 Options',
        'menu_title' 	=> '404 Options',
        'parent_slug' 	=> 'fundawande-options',
    ));
}

Timber::$dirname = array('templates', 'views');


class FundaWandeSite extends TimberSite {

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

        /**
         * User Context
         */

        $context['current_user'] = new Timber\User();

        /**
         * Login Context
         */

        if (isset($_GET['login'])) {
            if ( $_GET['login'] == 'failed') {
                $context['error_message'] = "incorrect_credentials";
            }
            else if ( $_GET['login'] == 'blank') {
                $context['error_message'] = "blank_field";
            }
        }

        //If language preference is not set and Ts and Cs are not signed, this returns false
        $context['user_meta_found'] = FundaWande()->login->fw_check_user_meta(); 


         /**
          * Language Context
          */

        // Set up language context to determine page language
        if ( isset($context['current_user']->language_preference)) {
            $context['lang'] = FundaWande()->language->get_language($context['current_user']->language_preference);
        }
        else {
            $context['lang'] = FundaWande()->language->get_language(null);
        }

        /**
         * LMS Context
         */

        $context['media_url'] = FundaWande()->lms->fw_get_media_url();

        /**
         * Menu Context
        */

        //Get current course link
        if ( isset($context['user']->fw_current_course)) {
            $context['course_id'] = FundaWande()->lms->fw_get_current_course_id($context['user']->ID);
            $context['course_link'] =  get_the_permalink($context['course_id']);
        }

        //Get current lesson link
        if ( isset($context['user']->fw_current_course) && isset($context['user']->fw_current_sub_unit) ) {

            $current_lesson_id = FundaWande()->lessons->fw_get_user_current_lesson($context['user']->ID);
            $context['current_lesson_link'] = get_the_permalink($current_lesson_id);
        }
        $context['menu'] = new TimberMenu();
        $context['learner_menu'] = new Timber\Menu('learner-menu');
        $context['coach_menu'] = new Timber\Menu('coach-menu');

        /** 
         * Misc Context
         */

        $context['options'] = get_fields('options');
        $context['is_mobile'] = wp_is_mobile();
        $context['site'] = $this;

        return $context;
    }

    function add_to_twig( $twig ) {
		/* this is where you can add your own functions to twig */
		//$twig->addExtension( new Twig_Extension_StringLoader() );
		//$twig->addFilter('myfoo', new Twig_SimpleFilter('myfoo', array($this, 'myfoo')));
		return $twig;
    }
    
}

new FundaWandeSite();