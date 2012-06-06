<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "welcome";
$route['404_override'] = '';
$route['users']='users/users_main';
$route['roles']='roles/roles_main';
$route['permissions']='permissions/permissions_main';
//$route['programs']='programs/programs_main';
//$route['users']='users/user_main';
$route['users_([a-z]+)']='users_$1/';
$route['users/users_([a-z]+)/(\d+)']='users/users_$1/$2';
$route['users_([a-z]+)/page/(\d+)']='users_$1/page/$2';
$route['users_([a-z]+)/uid/(\d+)']='users_$1/$2';
//$route['user_([a-z]+)/([a-z)+)/(:any)']='user_$1/$2/$3';
//$route['user_main/page/(:num)']='user_main/page/$1';
//$route['user_([a-z]+)/(\d+)']='users/user_$1/$2';
$route['roles/([a-z]+)']= "roles/role_$1";
//$route['permissions_add/(\d+)']= "permissions/permissions_add/$1";
//$route['roles/([a-z]+)/(\d+)'] = "roles/role_$1/$2";
//$route['permissions/([a-z]+)/(\d+)'] = "permissions/permissions_$1/$2";


/* End of file routes.php */
/* Location: ./application/config/routes.php */