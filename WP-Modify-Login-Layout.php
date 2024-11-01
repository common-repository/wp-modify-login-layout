<?php
/*
Plugin Name: Change WP login Layout
Plugin URI:
Description: WP Modify Login Layout: A very simple plugin to modify login page of wordpress.
Version: 1.0
Author: Athar Ahmed
Author URI: http://elantechnosys.com
License: GPLv2
*/
 
 Class wpchangelayout {
	 /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
	// Set class property
        $this->options = get_option( 'wp_modify_login_layout' );
		
		add_action( 'init', array( $this, 'init' ) );
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }
	
	public function init() {

		// Actions and filters
		add_action( 'login_enqueue_scripts', array( $this, 'wpmll_login_logo') );
		add_action( 'login_message', array( $this, 'wmll_login_message') );
		if(!empty($this->options['login_error_message'])){
		add_filter('login_errors', array( $this, 'login_error_message'));
		}
		if(!empty($this->options['url_title'])){
		add_action( 'login_headerurl', array( $this, 'wpmll_login_logo_url') );
		}
		if(!empty($this->options['login_logo_url_title'])){
		add_filter( 'login_headertitle', array( $this, 'wpmll_login_logo_url_title') );
		}
		if(!empty($this->options['wpmll_custom_background'])){
		add_action( 'login_enqueue_scripts', array( $this, 'wpmll_custom_background') );
		}
	}

    /**
     * Add options page
     */
    public function admin_menu()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'Change Login Page Layout', 
            'manage_options', 
            'wp-modify-login-layout', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>          
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'wp_modify_l' );   
                do_settings_sections( 'wp-modify-login-layout' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'wp_modify_l', // Option Setup
            'wp_modify_login_layout', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'WP Modify Login Page Layout', // url_title
            array( $this, 'print_section_info' ), // Callback
            'wp-modify-login-layout' // Page
        );  

        add_settings_field(
            'logo_url_title', // ID
            'Login Page logo image url', // url_title 
            array( $this, 'logo_image_callback' ), // Callback
            'wp-modify-login-layout', // Page
            'setting_section_id' // Section           
        );      

        add_settings_field(
            'url_title', 
            'Login Page logo url', 
            array( $this, 'logo_url_callback' ), 
            'wp-modify-login-layout', 
            'setting_section_id'
        );      
		
		add_settings_field(
            'login_logo_url_title', 
            'Login Page logo url title', 
            array( $this, 'login_logo_url_title_callback' ), 
            'wp-modify-login-layout', 
            'setting_section_id'
        ); 
		
		add_settings_field(
            'wmll_login_message', 
            'Login Page Message', 
            array( $this, 'login_message_callback' ), 
            'wp-modify-login-layout', 
            'setting_section_id'
        ); 
		
		add_settings_field(
            'login_error_messages', 
            'Custom Login Error Message', 
            array( $this, 'login_error_message_callback' ), 
            'wp-modify-login-layout', 
            'setting_section_id'
        ); 

		add_settings_field(
            'wpmll_custom_background', 
            'Custom background color of login page eg(#FFFFFF)', 
            array( $this, 'wpmll_custom_background_callback' ), 
            'wp-modify-login-layout', 
            'setting_section_id'
        ); 
		
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['logo_url_title'] ) )
            $new_input['logo_url_title'] = sanitize_text_field( $input['logo_url_title'] );

        if( isset( $input['url_title'] ) )
            $new_input['url_title'] = sanitize_text_field( $input['url_title'] );
			
		if( isset( $input['login_logo_url_title'] ) )
            $new_input['login_logo_url_title'] = sanitize_text_field( $input['login_logo_url_title'] );
			
		if( isset( $input['wmll_login_message'] ) )
            $new_input['wmll_login_message'] = sanitize_text_field( $input['wmll_login_message'] );
			
		if( isset( $input['login_error_message'] ) )
            $new_input['login_error_message'] = sanitize_text_field( $input['login_error_message'] );
			
		if( isset( $input['wpmll_custom_background'] ) )
            $new_input['wpmll_custom_background'] = sanitize_text_field( $input['wpmll_custom_background'] );
			

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Change login page layout below:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function logo_image_callback()
    {
        printf(
            '<input type="text" id="logo_url_title" name="wp_modify_login_layout[logo_url_title]" value="%s" />',
            isset( $this->options['logo_url_title'] ) ? esc_attr( $this->options['logo_url_title']) : ''
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function logo_url_callback()
    {
        printf(
            '<input type="text" id="url_title" name="wp_modify_login_layout[url_title]" value="%s" />',
            isset( $this->options['url_title'] ) ? esc_attr( $this->options['url_title']) : ''
        );
    }
	
	/** 
     * Get the settings option array and print one of its values
     */
    public function login_logo_url_title_callback()
    {
        printf(
            '<input type="text" id="login_logo_url_title" name="wp_modify_login_layout[login_logo_url_title]" value="%s" />',
            isset( $this->options['login_logo_url_title'] ) ? esc_attr( $this->options['login_logo_url_title']) : ''
        );
    }
	
	/** 
     * Get the settings option array and print one of its values
     */
    public function login_message_callback()
    {
        printf(
            '<input type="text" id="wmll_login_message" name="wp_modify_login_layout[wmll_login_message]" value="%s" />',
            isset( $this->options['wmll_login_message'] ) ? esc_attr( $this->options['wmll_login_message']) : ''
        );
    }
	
	/** 
     * Get the settings option array and print one of its values
     */
	public function login_error_message_callback()
	 {
        printf(
            '<input type="text" id="login_error_message" name="wp_modify_login_layout[login_error_message]" value="%s" />',
            isset( $this->options['login_error_message'] ) ? esc_attr( $this->options['login_error_message']) : ''
        );
    }
	
	/** 
     * Get the settings option array and print one of its values
     */
	public function wpmll_custom_background_callback()
	 {
        printf(
            '<input type="text" id="wpmll_custom_background" name="wp_modify_login_layout[wpmll_custom_background]" value="%s" />',
            isset( $this->options['wpmll_custom_background'] ) ? esc_attr( $this->options['wpmll_custom_background']) : ''
        );
    }
	
	public function wpmll_login_logo() {
	
	if(!empty( $this->options['logo_url_title'])){
    echo '<style type="text/css">
        body.login div#login h1 a {
            background-image: url('.esc_url( $this->options['logo_url_title']).');
			background-repeat: repeat;
			background-size: auto auto !important;
			height:100px !important;
			width:auto !important;
        }
    </style>';
	}
	}
	
	public function wpmll_login_logo_url() {
    if(!empty($this->options['url_title'])){
	return $this->options['url_title'];
	}
	}
	
	public function wpmll_login_logo_url_title() {
    if(!empty($this->options['login_logo_url_title'])){
	return $this->options['login_logo_url_title'];
	}
	}
	
	public function wmll_login_message() {
    if(!empty($this->options['wmll_login_message'])){
	return $this->options['wmll_login_message'];
	}
	}
	
	public function login_error_message() {
	if(!empty($this->options['login_error_message'])){
	return $this->options['login_error_message'];
	}
	}
	
	public function wpmll_custom_background() {
	if(!empty($this->options['wpmll_custom_background'])){
	echo '<style>body.login{ background: '.$this->options['wpmll_custom_background'].' !important;}</style>';
	}
	}
 
 }
 
 new wpchangelayout;