<?php
/**
 *Template Name: Video Archive Template
 *
 * @package Pango
 */


if ( class_exists( 'Timber' ) ) {

    $context = Timber::get_context();
    $post = new TimberPost();
    $context['post'] = $post;

    // Get Resource Categories
    $context['categories'] = Timber::get_terms('resource-categories');

    // No need for pagination yet, and it seems to be failing in any case. 

    // global $paged;
    // if (!isset($paged) || !$paged){
    //     $paged = 1;
    // }
    // $args = array(
    //     'post_type' => 'resource',
    //     'posts_per_page' => 5,
    //     'paged' => $paged
    // );

    $args = array(
        'post_type' => 'resource',
        'posts_per_page' => -1,
    );


    $resources = Timber::get_posts($args);
    $context['resources'] = $resources;
    $context['total_count'] = count($resources);


    Timber::render(array('template-public-video-resources.twig', 'page.twig'), $context);
}