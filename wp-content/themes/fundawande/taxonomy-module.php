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

    $user = new TimberUser();
    $context['user'] = $user;
    $context['term'] = $term;
    $current_course_id =  FundaWande()->lms->fw_get_current_course_id($user->ID);

    FundaWande()->language->fw_correct_module_lang($current_course_id,$term->ID);

    //Get module number to enable module-specific styling
    $context['module_number'] = get_term_meta($term->ID, 'module_number', true);
    $context['module_title'] = get_term_meta($term->ID, 'module_title', true);

    // Get the modules units to visualise on the module page
    $context['units'] = FundaWande()->modules->get_module_units($term->ID,$current_course_id);

    Timber::render(array('lms/single-module.twig', 'page.twig'), $context);
}
