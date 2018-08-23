<?php
/**
* @version   $Id: jvhelper.php 2015-08-13 [knigherrant] $
* @author Bold New Media http://www.boldnewmedia.com.au
* @copyright Copyright (C) 2008 - 2015 Bold New Media
* @support support@boldnewmedia.com.au
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
// add fucntion print for debug
 if(!function_exists('k')){
        function k($str){
                //if($_SERVER['REMOTE_ADDR'] == '14.161.35.175' || $_SERVER['REMOTE_ADDR'] == '::1' || $_SERVER['SERVER_NAME'] == 'localhost'){
                       if($str){
                               echo "<pre>";
                               print_R($str);
                               echo "</pre>";
                       }else{
                               echo "<pre>";
                               var_dump($str);
                               echo "</pre>";
                       }
               //}
        }
}

class PlgSystemJKHelper extends JPlugin{
    
   public function __construct(&$subject, $config){
        parent::__construct($subject, $config);
        
    }
    public function onAfterInitialise(){
	}
    public function onAfterRoute(){}
    public function onAfterDispatch(){}
    public function onAfterRender(){ }
    public function onBeforeRender(){}
    public function onBeforeCompileHead(){}
    public function onSearch(){}
    public function onSearchAreas(){} 
    
    public function rsfp_f_onAfterFormProcess($args = array()){
        $Itemid = JFactory::getApplication()->input->getInt('Itemid');
        $params = $this->params;

        if($Itemid){
            for ($i = 0; $i < 20; $i ++){
                if($params->get('form_' . $i) == $Itemid){
                    $menuid = $params->get('article_' . $i);
                    if($menuid) JFactory::getApplication()->redirect (JRoute::_ ('index.php?Itemid=' . $menuid));
                    return;
                }
            }
        }
    } 
    
}
    