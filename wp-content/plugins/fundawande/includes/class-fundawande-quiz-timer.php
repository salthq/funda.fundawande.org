<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Funda Wande Quiz Timer Class
 *
 * All functionality pertaining to displaying of the quiz timer in the LMS.
 *
 * @package Core
 * @author Pango
 *
 * @since 3.2.00
 */
class FundaWande_Quiz_Timer
{


    public function __construct()
    {

        // Add ajax calls to be accessed
        add_action('FUNDAWANDE_AJAX_HANDLER_quiz_start', array($this, 'quiz_start'));
        add_action('FUNDAWANDE_AJAX_HANDLER_nopriv_quiz_start', array($this, 'quiz_start'));
        add_action('FUNDAWANDE_AJAX_HANDLER_quiz_remain', array($this, 'quiz_remain'));
        add_action('FUNDAWANDE_AJAX_HANDLER_nopriv_quiz_remain', array($this, 'quiz_remain'));
        add_action('FUNDAWANDE_AJAX_HANDLER_quiz_time', array($this, 'quiz_time'));
        add_action('FUNDAWANDE_AJAX_HANDLER_nopriv_quiz_time', array($this, 'quiz_time'));
        add_action('FUNDAWANDE_AJAX_HANDLER_quiz_end', array($this, 'quiz_end'));
        add_action('FUNDAWANDE_AJAX_HANDLER_nopriv_quiz_end', array($this, 'quiz_end'));

        // Set ajax url variable
        add_action('wp_head', array($this, 'pluginname_ajaxurl'));

        // Initiate session
        add_action('wp', array($this, 'register_session'));
    }


    // Start quiz and set session
    public function quiz_start()
    {
        // quiz-start.php
        if (!isset($_SESSION)) {
            session_start();
        }
        if (!isset($_SESSION['quizStart'])) {
            unset($_SESSION['quizStart']);
            $_SESSION['quizStart'] = time();
        }
        echo json_encode($_SESSION['quizStart']);
    }

    // Determine and display remaining time
    public function quiz_time()
    {

        if (!isset($_SESSION)) {
            session_start();
        }
        $start_time = $_SESSION['quizStart'];
        $now = time();
        $end_time = $start_time + ($_SESSION['quizLimit'] * 60);
        $time_left = $end_time - $now;
        $_SESSION['quizRemaining'] = $time_left;
        if ($time_left >= 0) {

            echo json_encode($_SESSION['quizRemaining']);
        }
        die();
    }

    // Collect remaining time
    public function quiz_remain()
    {

        if (!isset($_SESSION)) {
            session_start();
        }
        if (isset($_SESSION['quizRemaining'])) {
            echo json_encode($_SESSION['quizRemaining']);
        }
        die();
    }

    // Make sure to unset session incase they restart quiz
    public function quiz_end()
    {

        if (!isset($_SESSION)) {
            session_start();
        }
        unset($_SESSION['quizStart']);
        echo json_encode('Time is up!');
        die();
    }


    // set ajax url variable
    public function pluginname_ajaxurl()
    {
        echo 
        '<script type="text/javascript">
            var ajaxurl = "' . admin_url("admin-ajax.php") . '"
        </script>';
    }

    // Make sure session is registered
    public function register_session()
    {

        if ((get_post_type() == 'quiz')) {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
        }
    }
}
