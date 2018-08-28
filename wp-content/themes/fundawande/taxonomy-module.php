<?php
/**
 * Single module page to show the children modules (sub-modules)
 *
 * @package Pango
 */

if (class_exists('Timber')) {
    $context = Timber::get_context();
    $term = new TimberTerm();

    // If a term parent exists then send it directly to the term parent
    if ($term->parent) {
        wp_redirect(get_term_link($term->parent));
        exit();
    } // End if, else show module and its units

    $context['user'] = new TimberUser();
    $context['term'] = $term;

    // Get the term data
    $context['term']->meta = get_term_meta( $term->ID);

    // Get the module children paths
    $context['term_children'] = get_term_children( $term->ID, 'module' );


    Timber::render(array('lms/single-module.twig', 'page.twig'), $context);
}
