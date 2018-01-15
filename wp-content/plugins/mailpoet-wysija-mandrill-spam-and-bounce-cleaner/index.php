<?php
/*
Plugin Name: MailPoet Newsletters - Mandrill Spam and Bounce Cleaner
Plugin URI: http://www.chrismedinaphp.com/plugins/mailpoet-mandrill-cleaner.zip
Description: Allows MailPoet users to easily unsubscribe or delete newsletter subscribers who have bounced, rejected, or reported you for spam. This will help keep your Mandrill reputation as high as possible by cleaning up errors. Works with MailPoet (formerly Wysija).
Version: 1.0
Author: Chris Medina
Author URI: http://www.chrismedinaphp.com/
License: GPLv2 or later
Text Domain: wysija-newsletters-cleaner
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
*/

//configured for wp-content/plugins/ directory to be moved, so you must use plugin_dir_path() and plugins_url() for absolute paths and URLs. See:

defined('ABSPATH') or die("No script kiddies please!");

define( 'WNC_REQUIRED_PHP_VERSION', '5.2.4' );  // because of WordPress minimum requirements
define( 'WNC_REQUIRED_WP_VERSION',  '3.1' );    // because of get_current_screen()

/**
 * Checks if the system requirements are met
 * @return bool True if system requirements are met, false if not
 */
function wnc_requirements_met() {
    global $wp_version;
    require_once( ABSPATH.DIRECTORY_SEPARATOR.'wp-admin'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'plugin.php' );

    if ( version_compare( PHP_VERSION, WNC_REQUIRED_PHP_VERSION, '<' ) ) {
        return false;
    }

    if ( version_compare( $wp_version, WNC_REQUIRED_WP_VERSION, '<' ) ) {
        return false;
    }

    if ( ! is_plugin_active( 'wysija-newsletters'.DIRECTORY_SEPARATOR.'index.php' ) ) {
        return false;
    }

    return true;
}

function wnc_requirements_error() {
    global $wp_version;

    require_once( dirname( __FILE__ ) .DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'requirements-error.php' );
}
/*
 * Check requirements and load main class
 */
if ( wnc_requirements_met() ) {
    require_once( dirname( __FILE__ ) .DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'base.php' );

} else {
    add_action( 'admin_notices', 'wnc_requirements_error' );
}