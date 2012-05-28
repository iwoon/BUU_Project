<?php if(!defined('BASEPATH')) exit('Not direct script access allowed');
class Logout extends CI_Controller
{
    public function __construct(){parent::__construct();}
    public function index()
    {
        redirect($this->frame->url.'logout');
    }
}
?>
