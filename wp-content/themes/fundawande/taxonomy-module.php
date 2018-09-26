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

    FundaWande()->language->fw_correct_module_lang($context['user']->fw_current_course,$term->ID);

    //Get module number to enable module-specific styling
    $context['module_number'] = get_term_meta($term->ID, 'module_number', true);
    $context['module_title'] = get_term_meta($term->ID, 'module_title', true);

    // Get the modules units to visualise on the module page
    // TODO: Remove the 31 and replace with the actual course ID, possibly saved in the user as their current course.
    $context['units'] = FundaWande()->modules->get_module_units($term->ID,31);
    

    Timber::render(array('lms/single-module.twig', 'page.twig'), $context);
}
