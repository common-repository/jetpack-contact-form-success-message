<?php

class JPCFSM
{    
    public $settings;

    public static function instance() {
        static $instance = null;
        
        if ( null === $instance ) {
            $instance = new JPCFSM;
            $instance->setup();
        }
        
        return $instance;
    }

    private function setup()
    {
        // early define constants
        $this->defineConstantsEarly();

        // load text domain
        $this->loadTextDomain();

        // define constants
        $this->defineConstants();

        // unslash, the one most import thing
        add_filter('jpcfsm_get_setting_message', 'wp_unslash');
        
        // decode message so it can parse HTML
        // wp_specialchars_decode somehow failed so I am using native
        add_filter('jpcfsm_get_setting_message', 'html_entity_decode');

        // automatically parse line breaks
        add_filter('jpcfsm_message_content_loaded', 'wpautop');

        // filter success display
        add_filter('grunion_contact_form_success_message', array($this, 'display'));

        // filter content
        if ( isset($_GET['contact-form-sent']) && (int) $_GET['contact-form-sent'] )
            add_filter('the_content', array($this, 'content'));
    }

    private function defineConstantsEarly() {

        if ( !defined('JPCFSM_BASE') ) {
            define('JPCFSM_BASE', plugin_basename( JPCFSM_FILE ));
        }

        return $this;        
    }

    public function loadTextDomain()
    {
        load_plugin_textdomain('jetpack-contact-form-success-message', false, dirname( JPCFSM_BASE ).'/languages' );

        return $this;        
    }

    private function defineConstants()
    {
        if ( !defined('JPCFSM_NAME') ) {
            // save me the hussle of typing it every time
            define( 'JPCFSM_NAME', __( 'DX Contact Form Success Page for Jetpack', 'jetpack-contact-form-success-message' ) );
        }

        return $this;        
    }

    public function flushSettings()
    {
        $this->settings = null;

        return $this;
    }

    public function settings($opt=null)
    {
        if ( !$this->settings ) {
            $this->settings = wp_parse_args(
                get_option('jpcfsm_settings'),
                array(
                    'message' => null,
                    'strip_content' => false
                )
            );
        }

        if ( $opt ) {
            $retval = isset( $this->settings[$opt] ) ? $this->settings[$opt] : null;
        } else {
            $retval = $this->settings;
        }

        $retval = apply_filters( 'jpcfsm_get_setting', $retval, $opt );
        $retval = apply_filters( 'jpcfsm_get_setting_' . $opt, $retval );

        return $retval;
    }

    public function display($message='')
    {
        $retval = $message;

        if ( $this->settings('message') ) {
            $retval = apply_filters('jpcfsm_message_content_loaded', $this->settings('message'));
        }

        return sanitize_text_field( $retval );
    }

    public function content($content='')
    {
        $retval = $content;

        if ( $this->settings('strip_content') && $content ) {
            $retval = $this->display();
        }

        return $retval;
    }
}