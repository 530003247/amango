<?php
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
define ( 'HEAD_URL', 'http://'.$_SERVER['SERVER_NAME'] );
define('AMANGO_FILE_ROOT',str_replace('\\','/',realpath(dirname(__FILE__).'/'))."/");
define ( 'APP_DEBUG', true );
define ( 'APP_PATH', './Application/' );
if(!is_file(APP_PATH . 'Common/Conf/config.php')){
	header('Location: ./install.php');
	exit;
}
define ( 'RUNTIME_PATH', './Runtime/' );
require './ThinkPHP/ThinkPHP.php';