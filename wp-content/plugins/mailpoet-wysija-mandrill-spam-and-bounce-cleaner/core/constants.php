<?php
/**
 * Created by PhpStorm.
 * User: production
 * Date: 1/19/15
 * Time: 2:54 AM
 */


//Constants

$plugin_name = 'wysija-newsletters-cleaner';
$plugin_folder_name = dirname( dirname(plugin_basename(__FILE__)) );
$current_folder = dirname( dirname(__FILE__) );

if(!defined('DS')) define( 'DS', DIRECTORY_SEPARATOR );

define( 'WNC', $plugin_name );

if(!defined('WNC_PLG_DIR')) define( 'WNC_PLG_DIR', dirname($current_folder).DS );

define( 'WNC_DIR', $current_folder.DS );

define( 'WNC_PLG_FOLDER_NAME', $plugin_folder_name );

define( 'WNC_CLASSES',WNC_DIR.'classes'.DS );

define( 'WNC_CONTROLLERS', WNC_DIR.'controllers'.DS );

define( 'WNC_MODELS', WNC_DIR.'models'.DS );

define( 'WNC_VIEWS', WNC_DIR.'views'.DS );

define( 'WNC_FILE', WNC_DIR.'index.php' );