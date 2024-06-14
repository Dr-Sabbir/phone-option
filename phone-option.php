<?php
/*
Plugin Name: Phone Option
Plugin URI:  https://github.com/Dr-Sabbir/phone-option
Description: A simple plugin to update phone number throughout the site using shortcode.
Version:     1.1
Author:      Dr. Sabbir
Author URI:  https://sabbirh.com/
Text Domain: phone-option
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

 
// Register settings
function phone_option_register_settings() {
    register_setting('phone_option_settings_group', 'phone_option_settings', 'phone_option_sanitize_settings');
}
add_action('admin_init', 'phone_option_register_settings');


// Add settings page
function phone_option_add_settings_page() {
    add_menu_page(
        __('Phone Option Settings', 'phone-option'),
        __('Phone Option', 'phone-option'),
        'manage_options',
        'phone-option',
        'phone_option_render_settings_page',
        'dashicons-admin-generic'
    );
}
add_action('admin_menu', 'phone_option_add_settings_page');


// Sanitize settings
function phone_option_sanitize_settings($settings) {
    $settings['phone_number'] = sanitize_text_field($settings['phone_number']);
    $settings['phone_number_two'] = sanitize_text_field($settings['phone_number_two']);
    $settings['custom_css'] = wp_strip_all_tags($settings['custom_css']); // Strips all tags from the custom CSS input
    return $settings;
}
// Render settings page
function phone_option_render_settings_page() {
    $options = get_option('phone_option_settings');
    ?>
    <div class="wrap">
        <h1><?php _e('Phone Option Settings', 'phone-option'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('phone_option_settings_group');
            do_settings_sections('phone_option_settings_group');
            if($options['custom_css']){
                $css = esc_textarea($options['custom_css']);
            }else{
                $css = '
                .phone_num{
                text-decoration: none;
                color: #000000 !important;
                transition: background-color 0.3s ease;
                }
                .phone_num:hover {
                color: #000000 !important;
                }
                ';
            }
            ?>

            Your Phone Number (may Contain Space and Other Charecters)<br>
            <input type="text" name="phone_option_settings[phone_number]" value="<?php echo esc_attr($options['phone_number'] ?? ''); ?>" /><br>

            Your Phone Number Without Any Spaces or Other Charecters<br>
            <input type="text" name="phone_option_settings[phone_number_two]" value="<?php echo esc_attr($options['phone_number_two'] ?? ''); ?>" /><br>

            Custom CSS<br>
            <textarea name="phone_option_settings[custom_css]" rows="10" cols="100"><?php echo $css; ?></textarea>
  
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}




function phone_fn($atts, $content = null) {
	extract(shortcode_atts(array( 'class'=>'' ), $atts));

    $options = get_option('phone_option_settings');
    $phone1 = $options['phone_number'];
    $phone2 = $options['phone_number_two'];
    $class = $atts['class'];

    $my_output = '';

    $my_output .= '<a class="phone_num '.$class.'" href="tel:'.$phone2.'">'.$phone1.'</a>';
   
  return $my_output;
}
add_shortcode('phone','phone_fn');


add_action('wp_head', 'wp_head_code');
function wp_head_code(){
    $options = get_option('phone_option_settings');
  echo '<style>'.$options['custom_css'].'</style>';
}