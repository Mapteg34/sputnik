<?php
/*
Plugin Name: Sputnik test plugin
Plugin URI: http://страница_с_описанием_плагина_и_его_обновлений
Description: Плагин для тестового задания sputnik.
Version: 1.0
Author: Mapt
Author URI: http://ibs1c.ru
 */

define('SPUTNIK__PLUGIN_DIR', plugin_dir_path(__FILE__));

require_once(SPUTNIK__PLUGIN_DIR.'class.sputnik.php');

$GLOBALS["sputnik"] = new Sputnik(__FILE__);

