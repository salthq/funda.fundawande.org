<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // security check, don't load file outside WP
}


/**
 * FundaWande Login
 *
 * All functionality pertaining to the login functionality of Funda Wande.
 *
 * @package Core
 * @author Pango
 *
 * @since 1.0.00
 */

class FundaWande_Login {

    /**
     * Constructor
     */
    public function __construct() {
        //Display the login form, so it can be added to the login page template.
        add_action('show_login_form', array($this,'setup_login_form'));
        //Check for wrong login information and add 'login=failed' to URL
        add_action('wp_login_failed', array($this, 'custom_login_failed'));
        //Check for blank fields and add 'login=blank-field' to URL
        add_action('authenticate', array($this, 'custom_login_blank_field'));
        // add custom login redirect
        add_action( 'wp_login', array($this,'fw_login_redirect') ,10,2);

    }

    /**
     * Redirect user after successful login.
     *
     * @param string $redirect_url URL to redirect to.
     * @param string $request URL the user is coming from.
     * @param object $user Logged user's data.
     * @return string
     */

    function fw_login_redirect( $user_login, $user  ) {
        //is there a user to check?
        $user_id =$user->ID;

        if ($user_id) {
            $current_lesson_id = FundaWande()->lessons->fw_get_user_current_lesson($user_id);
            $redirect_url = get_the_permalink($current_lesson_id);

            if (empty($redirect_url)) {
                // TODO HARDCODE IN FIRST LESSON
                $current_lessons = FundaWande()->lessons->fw_get_course_sub_units(586);
                $redirect_url = get_the_permalink($current_lessons[0]->ID);
            }

        } else {
            $redirect_url = home_url('/');
        }

        wp_redirect($redirect_url);
        exit();
    }


    //Set up login form options
    public function setup_login_form() {

        //TODO: Uncomment the bilingual form option lines. Perhaps move to ACF?
        $redirect_url = "/course/reading-for-meaning-eng/";
        $username_label = "ID Number";
        $password_label = "Password";
        $login_label = "Log In";
        // //Bilingual display and re-direct options
        // $redirect_url = "course/ukufunda-iintsingiselo-zesixhosa/";
        // $username_label = "Inombolo yesazisi";
        // $password_label = "Inombolo yokuvula";
        // $login_label = "Ngena";
        // if (isset($_GET['login_lang']) && $_GET['login_lang'] == 'eng') {
        //     $redirect_url = "/course/reading-for-meaning-eng/";
        //     $username_label = "ID Number";
        //     $password_label = "Password";
        //     $login_label = "Log In";
        // }


        $args = array(
            'echo'           => true,
            //TODO: add logic to redirect to coach dashboard if user logging in is a coach 
//            'redirect'       => home_url('/'),
            'form_id'        => 'fw-form-login',
            'label_username' => $username_label,
            'label_password' => $password_label,
            'label_remember' => __( 'Remember Me' ),
            'label_log_in'   => $login_label,
            'id_username'    => 'user_login',
            'id_password'    => 'user_pass',
            'id_remember'    => 'rememberme',
            'id_submit'      => 'fw-submit',
            'remember'       => true,
            'value_username' => NULL,
            'value_remember' => true );

        return wp_login_form( $args );
    } // end setup_login_form();

    /**
     * Refreshes the page and adds 'login=failed' if the info added failed to authenticate the user
     *
     * @param $user
     *
     * @author jtame
     */
    public function custom_login_failed ($user) {
        //If the login language is set to english, add it to the URL after stripping out other GET variables
        if( strstr($_SERVER['HTTP_REFERER'], 'eng') ) {
            $referrer = strtok($_SERVER['HTTP_REFERER'], '?');
            $referrer .= '?login_lang=eng';
        }
        else {
            //No need to check for Xhosa, as that is the default language
            $referrer = strtok($_SERVER['HTTP_REFERER'], '?');
        }
        if (!empty($referrer) && !strstr($referrer, 'wp-login') && !strstr($referrer, 'wp-admin') && $user!=null )
        {
            if (!strstr($referrer, '?login=failed'))
            {
                if(!strstr($referrer, '?'))
                {
                    $referrer .= '?';
                }
                else
                {
                    $referrer .= '&';
                }

                wp_redirect($referrer . 'login=failed');
            }
            else
            {
                wp_redirect($referrer);
            }

            exit;
        }
    } // end custom_login_failed();

    /**
     * Refreshes the page and adds 'login=failed' to the URL if either login field is blank
     *
     * @author jtame
     */
    public function custom_login_blank_field( ) {

        if(isset($_SERVER['HTTP_REFERER'])) {
            //If the login language is set to english, add it to the URL after stripping out other GET variables
            if( strstr($_SERVER['HTTP_REFERER'], 'eng') ) {
                $referrer = strtok($_SERVER['HTTP_REFERER'], '?');
                $referrer .= '?login_lang=eng';
            }
            else {
                //No need to check for Xhosa, as that is the default language
                $referrer = strtok($_SERVER['HTTP_REFERER'], '?');
            }

            $error = false;
            if(empty($_POST) || $_POST['log'] == '' || $_POST['pwd'] == '')
            {
                $error = true;
            }

            if (!empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') && $error)
            {
                if (!strstr($referrer, '?login=blank') )
                {
                    if(!strstr($referrer, '?'))
                    {
                        $referrer .= '?';
                    }
                    else
                    {
                        $referrer .= '&';
                    }

                    wp_redirect( $referrer . 'login=blank' );
                }
                else
                {
                    wp_redirect( $referrer );
                }

                exit;
            }
        }

    } // end custom_login_blank_field();

    public function check_if_active_language() {
        if (isset($_GET['login_lang']) && $_GET['login_lang'] == 'eng') {
            return "eng";
        }
        else {
            return "xho";
        }
    }

     /**
     * Check if user has a set language preference and that terms and conditions have been signed 
     * 
     * @return boolean $user_meta_found. if 'legal' and 'language_preference are found in 
     * the user meta, this boolean is set to true
     */
    public function fw_check_user_meta() {
        $user_id = get_current_user_id();

        $language_set = false;
        $terms_accepted = false;
        $user_meta_found = false;

        if(get_user_meta($user_id, 'language_preference', true) != "") {
            $language_set = true;
        }

        if(isset($_GET['legal'])) {
            update_user_meta($user_id, 'legal', 'agreed');
        }

        if(get_user_meta($user_id, 'legal', true) == 'agreed') {
            $terms_accepted = true;
        }

        if($language_set == true && $terms_accepted == true) {
            $user_meta_found = true;
        }

        return $user_meta_found;
    } // end fw_check_user_meta();

    /**
     * Register a new FW user
     *
     * @param string $user_login_ID number of new user.
     * @param string $first_name first name of new user.
     * @param string $last_name last name of new user.
     *
     * @return integer $user_id. if the user is added return the user ID, if not then false
     */
    public function fw_register_user($user_login, $first_name, $last_name) {

        // set the user email to be the username (ID) plus string
        $user_email = $user_login . '@fundawande.org';
        // if either the email or ID exists then return false
        if (email_exists( $user_email ) || username_exists($user_login)) {
            return false;
        }

        // set the password to be the same as username (ID)
        $password = $user_login;

        // set up the user data
        $userdata = array(
            'user_login' => $user_login,
            'user_email' => $user_email,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'user_pass' => apply_filters('pre_user_user_pass', $password),
            'role' => 'subscriber'
        );

        // inser user
        $user_id = wp_insert_user($userdata);

        // return user ID
        return $user_id;

    } // end fw_register_user();


} // end FundaWande_Login
