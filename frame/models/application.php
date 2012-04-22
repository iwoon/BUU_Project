<?php if(!defined('BASEPATH')) exit('Not direct access script allowed');

class Application extends CI_Controller{
    private $_app_config;
    public function __construct(){
        parent::__construct();
    }
    public function get_app_list(){
        return $this->db->get('app_installed')->result();
    }
    public function num_installed_app(){
        return $this->db->get('app_installed')->num_rows();
    }
    public function get_app_values($app=NULL){
        if(is_array($app)){
            $app=$this->db->get_where('app_installed',array('app_name'=>$app));
            return (($app->num_rows()>1)?$app->result():$app->row());
        }
    }
    public function check_app_installed($conf=NULL){
        if(!is_array($conf)||!empty($conf)){
            return (($this->db->get_where('app_installed',array('app_name'=>$conf))->num_rows()>0)?true:false);
        }
        if(is_array($conf)){
            $installed_app=array();
            foreach($conf as $app){
                array_push($installed,array(
                    'app_name'=>$app,
                    'installed'=>$this->db->get_where('app_installed',array('app_name'=>$app))->num_rows()
                ));
            }
            return $installed_app;
        }
        if(is_object($conf)){
            return (($this->db->get_where('app_installed',array('app_name'=>$conf->app_name))>0)?true:false);
        }
    }
    public function set_config($conf=NULL){
        if(!is_array($config)||empty($conf)){
            return false;
        }
        if(is_array($conf)||is_object($conf)){
            $is_installed= $this->db->get_where('app_installed',$conf)->num_rows();
            if(!$is_installed>0){
                //$this->db->insert('app_installed',$conf);
                $this->_app_config=$conf;
            }
        }else{return false;}
    }
    public function install($conf=NULL){
        if(is_array($conf)||is_object($conf)){
            $this->_app_config=$conf;
        }
        return $this->db->insert('app_installed',$this->_app_config);
    }
    //public function update_app(){}
}

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
