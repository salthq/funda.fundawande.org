<?php
//security first
if (!defined('ABSPATH')) exit;
/**
 * Funda Wande Quiz Timer Settings class
 *
 * This class handles all of the settings for the quiz timer functionality
 *
 * @package WordPress
 * @subpackage Sensei Quiz Timer
 * @category Core
 * @author Pango
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 * - __construct
 * - get_setting
 * - register_settings_tab
 * - register_settings_fields
 * todo go through all functions to make sure the info is correct
 */
class FundaWande_Quiz_Timer_Settings
{
    public function __construct()
    {
        if (is_admin()) {
            add_filter('sensei_settings_tabs', array($this, 'register_settings_tab'));
            add_filter('sensei_settings_fields', array($this, 'register_settings_fields'));

            add_action('admin_menu', array($this, 'pango_qt_add_meta_boxes'));

            add_action('save_post', array($this, 'pango_qt_save_meta'));
        }
    } // end __construct

    // Add meta box
    public function pango_qt_add_meta_boxes()
    {
        $qt_prefix = 'pango-qt_';
        global $qt_meta_box;
        $qt_meta_box = array(
            'id' => 'qt-meta-box',
            'title' => 'Sensei Quiz Timer',
            'page' => 'lesson',
            'context' => 'normal',
            'priority' => 'core',
            'fields' => array(
                array(
                    'name' => 'Quiz Time Limit',
                    'desc' => 'minutes',
                    'id' => $qt_prefix . 'limit',
                    'type' => 'number',
                    'std' => ''
                ),

            )
        );
        $quiz_enable = $this->get_setting('sensei_quiz_timer');
        if ($quiz_enable == true) {
            add_meta_box($qt_meta_box['id'], $qt_meta_box['title'], array($this, 'qt_show_box'), $qt_meta_box['page'], $qt_meta_box['context'], $qt_meta_box['priority']);
        }
    }

    public function qt_show_box()
    {
        global $qt_meta_box, $post;

        // Use nonce for verification
        echo '<input type="hidden" name="qt_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

        echo '<table class="form-table">';

        foreach ($qt_meta_box['fields'] as $qt_field) {
            // get current post meta data
            $meta = get_post_meta($post->ID, $qt_field['id'], true);

            echo '<tr>',
                '<th style="width:40%"><label for="',
                $qt_field['id'],
                '">',
                $qt_field['name'],
                '</label></th>',
                '<td>';
            switch ($qt_field['type']) {
                case 'text':
                    echo '<input type="text" name="', $qt_field['id'], '" id="', $qt_field['id'], '" value="', $meta ? $meta : $qt_field['std'], '" size="30" style="width:50%" />', '<br />', $qt_field['desc'];
                    break;
                case 'textarea':
                    $content = $meta ? $meta : $qt_field['std'];
                    $editor_id = $qt_field['id'];
                    $editor_settings = array(true, true, $qt_field['id'], 10, 0,);

                    wp_editor($content, $editor_id, $editor_settings);
                    /*echo '<textarea name="', $qt_field['id'], '" id="', $qt_field['id'], '" cols="60" rows="4" style="width:97%">', $meta ? $meta : $qt_field['std'], '</textarea>', '<br />', $qt_field['desc'];*/
                    break;
                case 'select':
                    echo '<select name="', $qt_field['id'], '" id="', $qt_field['id'], '">';
                    foreach ($qt_field['options'] as $option) {
                        echo '<option ', $meta == $option ? ' selected="selected"' : '', '>', $option, '</option>';
                    }
                    echo '</select>';
                    break;
                case 'radio':
                    foreach ($qt_field['options'] as $option) {
                        echo '<input type="radio" name="', $qt_field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'];
                    }
                    break;
                case 'checkbox':
                    echo '<input type="checkbox" name="', $qt_field['id'], '" id="', $qt_field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
                    break;
                case 'number':
                    echo '<input type="number" id="' . $qt_field['id'] . ' " name="' . $qt_field['id'] . '" class="small-text" value="', $meta ? $meta : $qt_field['std'], '"/>', $qt_field['desc'];
                    break;
                case 'time':
                    echo '<input type="time" id="' . $qt_field['id'] . ' " name="' . $qt_field['id'] . '" class="small-text" value="', $meta ? $meta : $qt_field['std'], '"/>';
                    break;
            }
            echo     '</td><td>',
                '</td></tr>';
        }

        echo '</table>';
    }

    // Save data from meta box
    public function pango_qt_save_meta($post_id)
    {
        global $qt_meta_box;
        if (isset($_POST['qt_meta_box_nonce'])) {
            // verify nonce
            if (!wp_verify_nonce($_POST['qt_meta_box_nonce'], basename(__FILE__))) {
                return $post_id;
            }
        }

        // check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        // check permissions
        if (isset($_POST['post_type'])) {
            if ('page' == $_POST['post_type']) {
                if (!current_user_can('edit_page', $post_id)) {
                    return $post_id;
                }
            } elseif (!current_user_can('edit_post', $post_id)) {
                return $post_id;
            }
        }

        if (isset($qt_meta_box['fields'])) {

            foreach ($qt_meta_box['fields'] as $qt_field) {
                $old = get_post_meta($post_id, $qt_field['id'], true);
                $new = false;
                if (isset($_POST[$qt_field['id']])) {
                    $new = $_POST[$qt_field['id']];
                }

                if ($new && $new != $old) {
                    update_post_meta($post_id, $qt_field['id'], $new);
                } elseif ('' == $new && $old) {
                    delete_post_meta($post_id, $qt_field['id'], $old);
                }
            }
        }
    }


    /**
     * sensei get_setting value wrapper
     *
     * @return string $settings value
     */
    public function get_setting($setting_token)
    {
        global $woothemes_sensei;

        // get all settings from sensei
        $settings = $woothemes_sensei->settings->get_settings();

        if (empty($settings)  || !isset($settings[$setting_token])) {
            return '';
        }

        return $settings[$setting_token];
    }

    /**
     * Attaches the quiz timer settings to the sensei admin settings tabs
     *
     * @param array $sensei_settings_tabs;
     * @return array  $sensei_settings_tabs
     */
    public function register_settings_tab($sensei_settings_tabs)
    {

        $smc_tab  = array(
            'name'             => __('Quiz Timer', 'sensei-quiz-timer'),
            'description'    => __('Optional settings for the Quiz Timer extension', 'sensei-quiz-timer')
        );

        $sensei_settings_tabs['sensei-quiz-timer-settings'] = $smc_tab;

        return $sensei_settings_tabs;
    } // end register_settings_tab


    /**
     * Includes the Quiz Timer settings fields
     *
     * @param array $sensei_settings_fields;
     * @return array  $sensei_settings_fields
     */
    public function register_settings_fields($sensei_settings_fields)
    {

        $sensei_settings_fields['sensei_quiz_timer'] = array(
            'name' => __('Enable', 'sensei-quiz-timer'),
            'description' => __('Check to activate quiz timer feature for Sensei lessons', 'woothemes-sensei'),
            'type' => 'checkbox',
            'default' => true,
            'section' => 'sensei-quiz-timer-settings'
        );

        return $sensei_settings_fields;
    } // end register_settings_tab
}// end Scd_Ext_settings
