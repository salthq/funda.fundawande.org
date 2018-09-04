<?php
/**
 *Template Name: Login Page Template
 *
 * @package Pango
 */

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

    Timber::render(array('template-login.twig', 'page.twig'), $context);
}