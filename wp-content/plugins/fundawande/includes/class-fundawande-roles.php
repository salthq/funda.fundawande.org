<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * FundaWande Roles Class
 *
 * This class handles the various roles available on Funda Wande
 *
 * @package Core
 * @author Pango
 *
 * @since 1.0.0
 */
class FundaWande_Roles
{
    /**
	 * Constructor.
	 * @since  1.0.0
	 */
    public function __construct()
    {
        //Remove editor capabilities
        add_action('init', array($this, 'fw_remove_editor_caps'));

        //Add editor capabilities
        add_action('init', array($this, 'fw_add_editor_caps'));
    } // end __construct()

    /**
     * Remove access to pages or comments for Funda Wande editors 
     */
    function fw_remove_editor_caps()
    {

        //Get the global role object
        $editor = get_role('editor');

        //List of capabilities to remove
        $caps = array(
            'delete_pages',
            'delete_posts',
            'delete_others_pages',
            'delete_others_posts',
            'delete_published_pages',
            'delete_published_posts',
            'edit_pages',
            'edit_others_pages',
            'edit_others_posts',
            'edit_private_pages',
            'edit_private_posts',
            'edit_published_pages',
            'edit_published_posts',
            'manage_links',
            'moderate_comments',
            'publish_pages',
            'publish_posts',
            'read_private_pages',
            'read_private_posts'
        );

        foreach ($caps as $cap) {
            //Remove the capability
            $editor->remove_cap($cap);
        }
    } // end fw_remove_editor_caps()

     /**
     * Remove access to pages or comments for Funda Wande editors 
     */
    function fw_add_editor_caps()
    {

        //Get the global role object
        $editor = get_role('editor');

		/**
		 * Editor capabilities array filter
		 *
		 * These capabilities will be applied to the teacher role
		 *
		 * @param array $capabilities
		 * keys: (string) $cap_name => (bool) $grant
		 */
		$caps =  array(
            // General access rules
            'manage_categories' => true,
			'read' => true,
			'manage_sensei_grades' => true,
			'moderate_comments' => true,
			'upload_files'	=> true,
			'edit_files'	=> true,

			// Lessons
			'publish_lessons'	 => true,
			'manage_lesson_categories'	 => true,
			'edit_lessons'	 => true,
			'edit_published_lessons'  => true,
			'edit_private_lessons' => true,
			'read_private_lessons' => true,
			'delete_published_lessons' => true,

            // Courses
			'create_courses' => true,
			'publish_courses'	 => true,
			'manage_course_categories'	 => true,
			'edit_courses'	 => true,
			'edit_published_courses'  => true,
			'edit_private_courses' => true,
			'read_private_courses' => true,
			'delete_published_courses' => true,

			// Quiz
			'publish_quizzes'	 => true,
			'edit_quizzes'	 => true,
			'edit_published_quizzes'  => true,
			'edit_private_quizzes' => true,
			'read_private_quizzes' => true,

			// Questions
			'publish_questions'	 => true,
			'edit_questions'	 => true,
			'edit_published_questions'  => true,
			'edit_private_questions' => true,
			'read_private_questions' => true,

			// messages
			'publish_sensei_messages'	 => true,
			'edit_sensei_messages'	 => true,
			'edit_published_sensei_messages'  => true,
			'edit_private_sensei_messages' => true,
            'read_private_sensei_messages' => true,
            

			// Comments -
			// Necessary cap so Teachers can moderate comments
			// on their own lessons. We restrict access to other
			// post types in $this->restrict_posts_menu_page()

		);

		foreach ( $caps as $cap => $grant ) {

			// load the capability on to the editor role
			$editor->add_cap( $cap, $grant );

		} // End foreach().

    } // end fw_remove_editor_caps()

} // end FundaWande_Roles 