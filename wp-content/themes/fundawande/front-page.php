<?php
/**
*Template Name: Home Page Template
 *
 * @package Pango
 */

if ( class_exists( 'Timber' ) ) {

    $context = Timber::get_context();
    $post = new TimberPost();
    $context['post'] = $post;

    // Set up language object to determine page language
    if ( isset($_GET['lang']) ) {
        $context['lang'] = FundaWande()->language->get_language($_GET['lang']);
    }
    else {
        $context['lang'] = FundaWande()->language->get_language(null);
    }

    Timber::render(array('template-home.twig', 'page.twig'), $context);

}