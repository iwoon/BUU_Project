<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|------------------------------------------------------------------------------
| Base Class Configuration
|------------------------------------------------------------------------------
| 
| You can choose if you always want to load the main jquery library when the
| class is loaded or not. If not, when you need the library you must call the
| add_jquery() method.
| You must set the path to the library, it can be stored on the filesystem
| (the absolute path from the base url must be specified, without the first slash)
| or from a web repository such as Google AJAX Api
|
| example: $config['jquery_ext']['main_library_path'] = "http://ajax.googleapis.com/ajax/libs/jquery/1.3.1/jquery.min.js"
|
| If you wish to include some js library from the local filesystem you can specify
| a prefix (relative to the document root) that is insered before the library name
| The library could use the jsmin-php class to minimize the script output, only on PHP5
|
*/
$config['jquery_ext']['auto_insert_jquery']		= TRUE;
$config['jquery_ext']['minimize_output']		= TRUE;
//$config['jquery_ext']['main_library_path']		= "http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js";		// without the first slash if is a local path.
$config['jquery_ext']['main_library_path']		= "http://woon.prj/frame/frame/asset/js/jquery.min.js";
$config['jquery_ext']['libraries_prefix']		= "";
$config['jquery_ext']['generate_js_files']		= FALSE;
$config['jquery_ext']['js_files_fs_path']		= 'js/output/';		// path on the filesystem where to store the js files
$config['jquery_ext']['js_files_url_prefix']	= config_item('base_url') . 'js/output/';	// prefix to prepend to the js files url

/*
|------------------------------------------------------------------------------
| Plugin and libraries configuration
|------------------------------------------------------------------------------
| 
| If you wish to use jquery plugins or generic javascript libraries you must
| configure them like the following lines.
| You must add an array in the config['jquery']['libraries'][your plugin name]
| specifing the needed informations like the plugin configured
|
*/

$config['jquery_ext']['libraries']['jquery_ui'] = array(
													"path"	  => 'http://woon.prj/frame/frame/asset/js/jquery-easyui-1.2.6/',
													"files"   => array (
                                                                                                                        'jquery.easyui.min.js',
                                                                                                                        'themes/default/easyui.css',
                                                                                                                        'themes/icon.css'
                                                                                                                    )
													);		
													
/*
|------------------------------------------------------------------------------
| Autoload libraries
|------------------------------------------------------------------------------
| 
| You can load some libraries every time you load the Jquery Class.
| Just add the name in the autoload array, the plugin must be configured properly
| in $config['jquery_ext']['libraries']
|
*/

$config['jquery_ext']['autoload'] = array('jquery_ui');

/*
|------------------------------------------------------------------------------
| Jquery Functions Dictionary
|------------------------------------------------------------------------------
| 
| Here you can add some functions with the %s where you want the code to be inserted
| Useful to have multiple insertion using the add_script function
|
*/

$config['jquery_ext']['functions'] = array(
										"general"		 => "%s",
										"document_ready" => "$('document').ready(function (){ %s });"
										
										);
