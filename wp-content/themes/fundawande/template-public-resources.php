<?php
/**
 *Template Name: Public Resources Template
 *
 * @package Pango
 */


if ( class_exists( 'Timber' ) ) {

    $context = Timber::get_context();
    $post = new TimberPost();
    $context['post'] = $post;

    $context['categories'] = Timber::get_terms('resource-categories');


    Timber::render(array('template-public-resources.twig', 'page.twig'), $context);
}