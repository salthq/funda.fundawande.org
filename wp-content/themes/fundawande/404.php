<?php
/**
 *  404 Page
 *
 * @package Pango
 */

if ( class_exists( 'Timber' ) ) {

    $context = Timber::get_context();
    $post = new TimberPost();
    $context['post'] = $post;
    Timber::render(array('404.twig', 'page.twig'), $context);

}