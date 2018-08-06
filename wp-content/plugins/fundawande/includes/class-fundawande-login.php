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


    //Set up login form options
    public function setup_login_form() {
        $args = array(
            'echo'           => true,
            'redirect'       => site_url($_SERVER['REQUEST_URI']),
            'form_id'        => 'form-login',
            'label_username' => __( 'ID Number' ),
            'label_password' => __( 'Password' ),
            'label_remember' => __( 'Remember Me' ),
            'label_log_in'   => __( 'Sign In' ),
            'id_username'    => 'user_login',
            'id_password'    => 'user_pass',
            'id_remember'    => 'rememberme',
            'id_submit'      => 'wp-submit',
            'remember'       => true,
            'value_username' => NULL,
            'value_remember' => true );

        return wp_login_form( $args );
    } // end setup_login_form();


} // end FundaWande_Login
