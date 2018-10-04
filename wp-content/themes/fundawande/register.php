<?php
/**
 *Template Name: Registration Page Template
 *
 * @package Pango
 */



if( is_user_logged_in() ) {
    wp_redirect(home_url('/'));
}

if ( class_exists( 'Timber' ) ) {

    $context = Timber::get_context();
    $post = new TimberPost();
    $context['post'] = $post;


    // Set up language object to determine login page language
    if ( isset($_GET['login_lang']) ) {
        $context['lang'] = FundaWande()->language->get_language($_GET['login_lang']);
    }
    else {
        $context['lang'] = FundaWande()->language->get_language(null);
    }

    if( isset($_POST['action']) && $_POST['action'] == 'register') {

        $context['register_submitted'] = true;

        // Sanitize the fields
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $user_login = sanitize_text_field($_POST['user_login']);

        if (!empty($user_login)) {
            $user_id = FundaWande()->login->fw_register_user($user_login, $first_name, $last_name);

            if (!$user_id) {
                $context['register_failed'] = true;
            }
        } else {
            $context['form_error'] = true;
        }
    }
    Timber::render(array('template-register.twig', 'page.twig'), $context);
}