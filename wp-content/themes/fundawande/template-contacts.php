<?php
/**
 *Template Name: Contacts Page Template
 *
 * @package Pango
 */


if ( class_exists( 'Timber' ) ) {

    $context = Timber::get_context();
    $post = new TimberPost();
    $context['post'] = $post;


    Timber::render(array('template-contacts.twig', 'page.twig'), $context);
}