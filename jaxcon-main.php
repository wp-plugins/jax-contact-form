<?php
/*
Plugin Name: Jax Contact Form With ReCaptcha
Plugin URI: http://www.karthik.sg/wp_projects/jaxcon/
Plugin Description: Setup a secured validated Jax Contact Form with Recaptcha. 
Plugin Version: 1.0
Plugin Author: Alagappan Karthikeyan (JACK)
Plugin Author URI: http://www.karthik.sg/
Plugin Wordpress Profile : http://profiles.wordpress.org/users/karthiksg

    Date: 16 Oct 2011

    Copyright 2011  All Rights Reserved.
    Name : Alagappan Karthikeyan
    Email : me@karthik.sg
    Website : www.karthik.sg
    Demo    : www.karthik.sg/wp_projects/jaxcon/demo/
    support : www.karthik.sg/wp_projects/jaxcon/demo/support/
    Screenshots : www.karthik.sg/wp_projects/jaxcon/demo/gallery/
    Video Instructions : www.karthik.sg/wp_projects/jaxcon/demo/vdo
	
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation;

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

// Admin Options Page

add_action('admin_menu', 'plugin_admin_add_page');

function plugin_admin_add_page() {

	add_options_page(
                       'Contact Form With Captcha Plugin Page', 
                       'Jax Contact Form', 
                       'manage_options', 
                       'contact-form-with-captcha', 
                       'plugin_options_page'
                      );

}

// display the admin options page
function plugin_options_page() {
?>

<div>
	<h2>Jax Contact Form With ReCaptcha - Settings</h2>
	<form action="options.php" method="post">
            <?php settings_fields('jaxcon_options_group'); ?>
            <?php do_settings_sections('contact-form-with-captcha'); ?>
	<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
	</form>
</div>

<?php 
}

// add the admin settings and such

add_action('admin_init', 'plugin_admin_init');


function plugin_admin_init(){

	register_setting( 'jaxcon_options_group', 'jaxcon_private_key_value', 'plugin_options_validate' );
	register_setting( 'jaxcon_options_group', 'jaxcon_public_key_value',  'plugin_options_validate' );
	register_setting( 'jaxcon_options_group', 'jaxcon_to_value',          'plugin_options_validate' );
	register_setting( 'jaxcon_options_group', 'jaxcon_subject_value',     'plugin_options_validate' );


	add_settings_section('plugin_main', 'Main Settings1', 'testjack', 'contact-form-with-captcha');

	add_settings_section('plugin_main', 'Main Settings', 'plugin_section_text', 'contact-form-with-captcha');


	add_settings_field('jaxcon_private_key_field_id', 'Specify your private key',  'jaxcon_private_key_field_callback', 'contact-form-with-captcha', 'plugin_main');
	add_settings_field('jaxcon_public_key_field_id',  'Specify your public key',   'jaxcon_public_key_field_callback',  'contact-form-with-captcha', 'plugin_main');
	add_settings_field('jaxcon_to_field_id',          'Specify your email address','jaxcon_to_field_callback',          'contact-form-with-captcha', 'plugin_main');
	add_settings_field('jaxcon_subject_field_id',     'Specify predefined subjects (,) eg. Enquiry,Sales,Others','jaxcon_subject_field_callback', 'contact-form-with-captcha', 'plugin_main');

}

function plugin_section_text() {
        echo "<iframe src='http://www.karthik.sg/wp_projects/jaxcon/support/video/howto.swf' width='500' height='400'></iframe>";
	echo '<p>Specify your captcha key (<a href="https://www.google.com/recaptcha/admin/create" target=_blank>Get a Free Key</a> )</p>';
	
} 

function jaxcon_to_field_callback() {

	$options = get_option('jaxcon_to_value');
	echo "<input id='jaxcon_to_field_id' name='jaxcon_to_value[text_string]' size='40' type='text' value='{$options['text_string']}' />";

}

function jaxcon_private_key_field_callback() {

	$options = get_option('jaxcon_private_key_value');
	echo "<input id='jaxcon_private_key_field_id' name='jaxcon_private_key_value[text_string]' size='40' type='text' value='{$options['text_string']}' />";

}

function jaxcon_public_key_field_callback()  {

      $options = get_option('jaxcon_public_key_value');
      echo "<input id='jaxcon_public_key_field_id' name='jaxcon_public_key_value[text_string]' size='40' type='text' value='{$options['text_string']}' />";
}

function jaxcon_subject_field_callback()  {

      $options = get_option('jaxcon_subject_value');
      echo "<input id='jaxcon_subject_field_id' name='jaxcon_subject_value[text_string]' size='100' type='text' value='{$options['text_string']}' />";
}


// validate our options
function plugin_options_validate($input) {

	$newinput['text_string'] = trim($input['text_string']);
//	if(!preg_match('/^[a-z0-9]{32}$/i', $newinput['text_string'])) {
//		$newinput['text_string'] = '';
//	}
	return $newinput;
}

// [jaxcon publickey="abc" privatekey="def"]

add_shortcode( 'jaxcon', 'jaxcon_func' );
function jaxcon_func( $atts ) {
	extract( shortcode_atts( array(
		'publickey' => 'something',
		'privatekey' => 'something else',
	), $atts ) );
      
      ob_start();
      $privatekey   = get_option('jaxcon_private_key_value');
      $publickey    = get_option('jaxcon_public_key_value');
      $jaxcon_to      = get_option('jaxcon_to_value');
      $jaxcon_subject = get_option('jaxcon_subject_value');

      $privatekey   = $privatekey['text_string'] ;
      $publickey    = $publickey['text_string'] ;
      $jaxcon_to      = $jaxcon_to['text_string'];
      $jaxcon_subject = $jaxcon_subject['text_string'];


      include(WP_PLUGIN_DIR . '/jax-contact-form/jaxcon-form.php');
    
      $output_string=ob_get_contents();
      ob_end_clean();

      return $output_string;
}
?>
