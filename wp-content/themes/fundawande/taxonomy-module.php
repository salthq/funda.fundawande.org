<?php
/**
 * Single module page to show the children modules (units)
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

    $context['module_number'] = get_term_meta($term->ID, 'module_number', true);

    // Get the modules units to visualise on the module page
    // TODO: Remove the 31 and replace with the actual course ID, possibly saved in the user as their current course.
    $context['units'] = FundaWande()->modules->get_module_units($term->ID,31);
    

    Timber::render(array('lms/single-module.twig', 'page.twig'), $context);
}
