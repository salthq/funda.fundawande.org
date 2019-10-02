<?php
/**
 *Template Name: Terms and Conditions
 *
 * @package Pango
 */

if ( class_exists( 'Timber' ) ) {

    $context = Timber::get_context();
    $post = new TimberPost();
    $context['post'] = $post;
    Timber::render(array('terms-and-conditions.twig', 'page.twig'), $context);

}