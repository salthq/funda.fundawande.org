<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * FundaWande Admin Class
 *
 * All functionality pertaining to the admin of FundaWande.
 *
 * @package Core
 * @author Pango
 *
 * @since 1.0.0
 */
class FundaWande_Admin
{

    /**
     * Constructor.
     * @since  1.0.0
     */
    public function __construct()
    {
		// Scripts and Styles
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

        //Restrict dashboard visibility
        add_action('admin_init', array($this, 'fw_restrict_dashboard_visibility'));

        //Remove add media button from ACF fields
        add_action('acf/input/admin_head', array($this, 'fw_remove_media_buttons'));

        //Remove Lesson information meta box
        add_action('admin_menu', array($this, 'fw_remove_lesson_info_metabox'), 100);

        //Remove the description input from module editor
        add_action('module_add_form', array($this, 'fw_remove_description_input'), 100);
        add_action('module_edit_form', array($this, 'fw_remove_description_input'), 100);
        
        //Remove description column from module editor columns
        add_action('manage_edit-module_columns', array($this, 'fw_remove_description_column'), 100, 1);
        
        //Change the placeholder text for lesson titles
        add_filter('enter_title_here', array($this, 'fw_change_lesson_title_text'), 100, 2);
    } // End __construct()

    /**
     * Enqueue admin scripts.
     */
    public function admin_enqueue_scripts()
    {
        wp_enqueue_script('theme-admin-script', FundaWande()->plugin_url . 'assets/js/admin.min.js', array('jquery'), FundaWande()->version, true);
    }

    /**
     * Remove add media buttons from ACF
     */
    function fw_remove_media_buttons()
    {
        remove_action('media_buttons', 'media_buttons');
    } // end fw_remove_media_buttons();

    /**
     * Disallow subscribers from viewing the admin dashboard
     */
    function fw_restrict_dashboard_visibility()
    {
        $redirect = home_url('/');
        if (!current_user_can('administrator') && !is_super_admin() && !current_user_can('editor') && $_SERVER['PHP_SELF'] != '/wp-admin/admin-ajax.php') {
            exit(wp_redirect($redirect));
        }
    } // end fw_restrict_dashboard_visibility()

    /**
     * Remove the lesson information metabox from the WooSensei lesson editor
     */
    function fw_remove_lesson_info_metabox()
    {
        $lesson = Sensei()->lesson->token;
        remove_meta_box('lesson-info', $lesson, 'normal');
    } // end fw_remove_lesson_info_metabox()

    /**
     * Remove the description input from module editor 
     */
    function fw_remove_description_input()
    {
        ?>
            <style>.term-description-wrap{display:none;}</style>
        <?php

    } // end fw_remove_description_input()

    /**
     * Remove description column from module editor columns
     */
    function fw_remove_description_column($columns)
    {
        if (isset($columns['description']))
            unset($columns['description']);

        return $columns;
    } // end fw_remove_description_column()

    /**
     * Change the placeholder text for lesson titles
     */
    function fw_change_lesson_title_text($title, $post)
    {

        if ($post->post_type == 'lesson') {
            $title = 'Enter the internal title for this lesson here (this will not be visible to users)';
        }

        return $title;
    } // End fw_change_lesson_title_text()


} // End FundaWande_Admin Class
