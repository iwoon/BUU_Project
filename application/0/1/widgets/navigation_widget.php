<?php if(!defined('BASEPATH')) exit('Not direct script access allowed');
class Navigation_widget extends Widget{
    public function display(){
        $data['navigation_data']=$this->frame->nav->get();
        $this->load->view('widgets/navigation_bar',$data);
    }
}
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
