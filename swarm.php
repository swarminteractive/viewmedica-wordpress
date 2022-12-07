<?php

/*
Plugin Name: ViewMedica Embed
Plugin URI: http://viewmedica.com/
Description: Allows easy embed of ViewMedica 8 into WordPress Posts and Pages. A full description, info and pull requests can be found at https://github.com/swarminteractive/viewmedica-wordpress
Version: 1.4.15
Author: Swarm Interactive, Inc.
Author URI: http://swarminteractive.com/

Copyright 2022 SWARM_INTERACTIVE

(email : support@swarminteractive.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function swarm_install() {

    global $wpdb;
    $swarm_db_version = '1.4.7';
    $installed_version = get_option('swarm_db_version');

    if ($swarm_db_version !== $installed_version) {
      $table_name = $wpdb->prefix . 'viewmedica';
      $charset_collate = $wpdb->get_charset_collate();
      require_once( ABSPATH . 'wp-admin/includes/upgrade.php');

      $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        vm_id mediumint(9) DEFAULT 0,
        vm_width mediumint(9) DEFAULT 720,
        vm_secure tinyint(1) DEFAULT 1,
        vm_brochures tinyint(1) DEFAULT 1,
        vm_fullscreen tinyint(1) DEFAULT 1,
        vm_disclaimer tinyint(1) DEFAULT 1,
        vm_visible tinyint(1) DEFAULT 1,
        vm_language varchar(10) DEFAULT 'en',
        PRIMARY KEY  (id)
      ) $charset_collate;";

      dbDelta($sql);

      $sql = "SELECT * FROM " . $table_name . "
              WHERE id = 1";
      $result = $wpdb->get_results($sql, 'OBJECT');

      if (!count($result)) {
        add_user_meta($user_id, 'swarm_ignore_notice', 'false', true);
        $rows_affected = $wpdb->insert( $table_name, array('id' => 1, 'vm_id' => '', 'vm_width' => 720, 'vm_secure' => 1, 'vm_fullscreen' => 1, 'vm_brochures' => 1, 'vm_disclaimer' => 1, 'vm_visible' => 1, 'vm_language' => 'en' ) );
      }
    }

    update_option('swarm_db_version', $swarm_db_version);

}

function swarm_settings() {

    include_once('swarm-admin.php');

}

function swarm_admin_actions() {

    add_options_page("ViewMedica", "ViewMedica", "customize", "Viewmedica-Admin", "swarm_settings");

}

function swarm_viewmedica_display($atts = null, $content = null) {

    wp_register_script('vm_event_listener', plugins_url( 'vm_event_listener.js' , __FILE__ ), array(), '1.1.0', true );
    wp_enqueue_script( 'vm_event_listener' );

    global $wpdb;
    $table_name = $wpdb->prefix . 'viewmedica';

    $sql = "SELECT * FROM " . $table_name . "
            WHERE id = 1";
    $result = $wpdb->get_results($sql, 'OBJECT');
    $result = $result[0];
    $client = $result->vm_id;
    $global_width = $result->vm_width;
    $secure = $result->vm_secure;
    $brochures = $result->vm_brochures;
    $fullscreen = $result->vm_fullscreen;
    $disclaimer = $result->vm_disclaimer;
    $visible = $result->vm_visible;
    $language = $result->vm_language;

    if ( $visible === 0 || $visible === "0" ) {
        return '';
    }

    $param_string = '';

    $param_string .= 'client: ' . $client . ', ';

    if( $secure == 1 ) {
        $param_string .= 'secure: "true", ';
    }

    if( $brochures == 0 ) {
        $param_string .= 'brochures: "false", ';
    }

    if( $fullscreen == 0 ) {
        $param_string .= 'fullscreen: "false", ';
    }

    if( $disclaimer == 0 ) {
        $param_string .= 'disclaimer: "false", ';
    }

    if( $language != 'en' ) {
        $param_string .= 'lang: "'. $language . '", ';
    }

    if( $atts != null ) {
        $a = shortcode_atts( array( 'menuaccess' => '', 'openthis' => '', 'width' => '', 'audio' => '', 'captions' => '', 'subtitles' => '', 'markup' => '', 'search' => '', 'sections' => '', 'sharing' => '', 'autoplay' => '' ), $atts );
        $openthis = $a['openthis'];
        $menuaccess = $a['menuaccess'];
        $width = $a['width'];
        $audio = $a['audio'];
        $captions = $a['captions'];
        $subtitles = $a['subtitles'];
        $markup = $a['markup'];
        $search = $a['search'];
        $sections = $a['sections'];
        $sharing = $a['sharing'];
        $autoplay = $a['autoplay'];

        if ($audio != '') {
            $param_string .= 'audio: '. $audio . ', ';
        }
        if ($captions != '') {
            $param_string .= 'captions: '. $captions . ', ';
        }
        if ($subtitles != '') {
            $param_string .= 'subtitles: '. $subtitles . ', ';
        }
        if ($markup != '') {
            $param_string .= 'markup: '. $markup . ', ';
        }
        if ($search != '') {
            $param_string .= 'search: '. $search . ', ';
        }
        if ($sections != '') {
            $param_string .= 'sections: '. $sections . ', ';
        }
        if ($sharing != '') {
            $param_string .= 'social: '. $sharing . ', ';
        }
        if ($autoplay != '') {
            $param_string .= 'autoplay: '. $autoplay . ', ';
        }
        if( $menuaccess != '' ) {
            $param_string .= 'menuaccess: "false", ';
        }

        if($openthis != '') {
            $viewmedica_div = "<div id='" . $openthis . "'></div>";
            $param_string .= 'openthis: "' . $openthis . '", ';
        } else {
            $viewmedica_div = "<div id='vm'></div>";
            $param_string .= 'openthis: "vm", ';
        }

        if( $width != '' ) {
            $param_string .= 'width: ' . $width . ', ';
        } else {
            $param_string .= 'width: ' . $global_width . ', ';
        }

        $viewmedica_div .= "<script type='text/javascript'>" . "vm_open({" . $param_string . "});</script>\n<!-- ViewMedica Embed End -->";

    } else {

        $viewmedica_div = "<div id='vm'></div>";

        $viewmedica_div .= "<script type='text/javascript'>vm_open({client: \"" . $client . "\", width:\"" . $global_width . "\"" . "});</script>\n<!-- ViewMedica Embed End -->";

    }

    return $viewmedica_div;
}

function swarm_header() {

    wp_register_script('viewmedicascript', 'https://ondemand.viewmedica.com/lib/vm.js', array(),
       '1.1', false );
    wp_enqueue_script( 'viewmedicascript' );

}

function add_viewmedica_button() {
    // Don't bother doing this stuff if the current user lacks permissions
    if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
        return;

    // Add only in Rich Editor mode
    if ( get_user_option('rich_editing') == 'true') {
        add_filter("tiny_mce_before_init", "add_viewmedica_clientid");
        add_filter("mce_external_plugins", "add_viewmedica_tinymce_plugin");
        add_filter('mce_buttons', 'register_viewmedica_button');
    }
}

function add_viewmedica_clientid($settings)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'viewmedica';

    $sql = "SELECT * FROM " . $table_name . "
            WHERE id = 1";
    $result = $wpdb->get_results($sql, 'OBJECT');
    $result = $result[0];

    $settings['vm_client'] = $result->vm_id;
    $settings['vm_width'] = $result->vm_width;
    $settings['vm_secure'] = $result->vm_secure;
    $settings['vm_brochures'] = $result->vm_brochures;
    $settings['vm_fullscreen'] = $result->vm_fullscreen;
    $settings['vm_disclaimer'] = $result->vm_disclaimer;
    $settings['vm_language'] = $result->vm_language;

    return $settings;
}

function register_viewmedica_button($buttons) {
    array_push($buttons, "|", "viewmedica");
    return $buttons;
}

// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
function add_viewmedica_tinymce_plugin($plugin_array) {
    $plugin_array['viewmedica'] = plugins_url() .'/viewmedica/swarm_plugin.js';
    return $plugin_array;
}

function vm_refresh_mce($ver) {
    $ver += 3;
    return $ver;
}

function swarm_admin_notice() {
    global $current_user ;
        $user_id = $current_user->ID;
        /* Check that the user hasn't already clicked to ignore the message */
    if ( ! get_user_meta($user_id, 'swarm_ignore_notice') ) {
        echo '<div class="updated"><p>';
        printf(__('You need to update ViewMedica (in Settings -> ViewMedica) with your client ID before using. | <a href="%1$s">Hide Notice</a>'), '?swarm_nag_ignore=0');
        echo "</p></div>";
    }
}

function swarm_nag_ignore( $updated = false ) {
    global $current_user;
        $user_id = $current_user->ID;
        /* If user clicks to ignore the notice, add that to their user meta */
        if ( isset($_GET['swarm_nag_ignore']) && '0' == $_GET['swarm_nag_ignore'] || $updated ) {
             add_user_meta($user_id, 'swarm_ignore_notice', 'true', true);
    }
}

function swarm_uninstall() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'viewmedica';
    $sql = 'DROP TABLE ' . $table_name;
    $wpdb->query($sql);
}

register_uninstall_hook(__FILE__, 'swarm_uninstall');
add_action('admin_notices', 'swarm_admin_notice');
add_action('admin_init', 'swarm_nag_ignore');

// init process for button control
add_filter( 'tiny_mce_version', 'vm_refresh_mce');
add_action('init', 'add_viewmedica_button');

//allows for [viewmedica] shortcode
add_shortcode('viewmedica', 'swarm_viewmedica_display');

//add display functionality
add_action('wp_enqueue_scripts', 'swarm_header');

//insert swarm admin panel into 'settings'
add_action('admin_menu', 'swarm_admin_actions');
add_action('plugins_loaded', 'swarm_install');

function thickbox() 
{
    add_thickbox();
}

add_action('admin_enqueue_scripts', 'thickbox');


register_activation_hook(__FILE__, 'swarm_install');

?>
