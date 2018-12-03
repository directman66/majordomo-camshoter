<?php
/**
* @author camshoter by sannikov dmitriy <sannikovdi@ya.ru>
* @package project
* @author Wizard <sergejey@gmail.com>
* @copyright http://majordomo.smartliving.ru/ (c)
* @version 0.1 (wizard, 10:01:31 [Jan 03, 2018])
*/
//
//
//ini_set('max_execution_time', '600');
//ini_set ('display_errors', 'off');
class camshoter extends module {
/**
*
* Module class constructor
*
* @access private
*/
function camshoter() {
  $this->name="camshoter";
  $this->title="camshoter";
  $this->module_category="<#LANG_SECTION_APPLICATIONS#>";
  $this->checkInstalled();
}
/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
function saveParams($data=0) {
 $p=array();
 if (IsSet($this->id)) {
  $p["id"]=$this->id;
 }
 if (IsSet($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (IsSet($this->edit_mode)) {
  $p["edit_mode"]=$this->edit_mode;
 }
 if (IsSet($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}
/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;
  global $mode;
  global $view_mode;
  global $edit_mode;
  global $tab;


  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }

}
/**
* Run
*
* Description
*
* @access public
*/
function run() {
 global $session;
  $out=array();
  if ($this->action=='admin') {
   $this->admin($out);
  } else {
   $this->usual($out);
  }
  if (IsSet($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (IsSet($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  $out['TAB']=$this->tab;
  $this->data=$out;



  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;
}
/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {


	




 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }


$this->searchdevices($out, $this->id);




 if ($this->view_mode=='indata_edit') {
   $this->editdevices($out, $this->id);
 }





 if ($this->view_mode=='indata_del') {
   $this->delete($this->id);
//   $this->redirect("?data_source=$this->data_source&view_mode=node_edit&id=$pid&tab=indata");
   $this->redirect("?");
 }	




}

  




 



 function indata_edit(&$out, $id) {
  require(DIR_MODULES.$this->name.'/indata_edit.inc.php');
 }
 
 function searchdevices(&$out) {


$mhdevices=SQLSelect("SELECT * FROM camshoter_devices");
$total = count($mhdevices);
for ($i = 0; $i < $total; $i++)
{ 
$ip=$mhdevices[$i]['IPADDR'];
$lastping=$mhdevices[$i]['LASTPING'];
//echo time()-$lastping;
if (time()-$lastping>300) {
$online=ping(processTitle($ip));
    if ($online) 
{SQLexec("update camshoter_devices set ONLINE='1', LASTPING=".time()." where IPADDR='$ip'");} 
else 
{SQLexec("update camshoter_devices set ONLINE='0', LASTPING=".time()." where IPADDR='$ip'");}
}}



  require(DIR_MODULES.$this->name.'/search.inc.php');
 }

// function updatecurrent(&$out) {




  
 
/**
* FrontEnd
*
* Module frontend
*
* @access public
*/
function usual(&$out) {
 $this->admin($out);
}


 function propertySetHandle($object, $property, $value) {
//   sg('test.camshoter','123');

   $table='camshoter_devices';
$sql="SELECT * FROM $table WHERE LINKED_OBJECT LIKE '".DBSafe($object)."' AND LINKED_PROPERTY LIKE '".DBSafe($property)."'";
   $properties=SQLSelect($sql);
//   sg('test.camshoter',  $sql);
//   sg('test.camshoter',  $properties['TYPE']);

   $total=count($properties);
   if ($total) {
    for($i=0;$i<$total;$i++) {
	 if($properties[$i]['ID']) {
//		sg($properties[$i]['TARGET_OBJECT'].'.'.$properties[$i]['TARGET_PROPERTY'], (int)!$value);
//	 } else { 
//		sg($properties[$i]['TARGET_OBJECT'].'.'.$properties[$i]['TARGET_PROPERTY'], $value);


//для теста вызовем нужный метод датчика движения
//cm('Motion11.motionDetected');




if ($properties[$i]['TYPE']=='snapshot')
{

$image_url=$properties[$i]['URL'];

//   sg('test.camshoter',  $properties['TYPE']);


$savepath=ROOT."cms/cached/nvr/cam".$properties[$i]['ID'].'/';
//   sg('test.camshoter', $savepath);


 if (!file_exists($savepath)) {
mkdir($savepath, 0777, true);}

$savename=$savepath.date('YmdHis').".jpg"; // куда сохранять
SaveFile($savename, $result);

$text='Зафиксировано движение '.$properties['TITLE'];
include_once(DIR_MODULES . 'telegram/telegram.class.php');
$telegram_module = new telegram();
$telegram_module->sendImageToAll($save_to,$text);
}


	 }
	 $properties[$i]['UPDATED']=date('Y-m-d H:i:s');
	 SQLUpdate('camshoter_devices', $properties[$i]);
    }
   }
 }



/**

*
* @access public
*/

 function delete($id) {
  $rec=SQLSelectOne("SELECT * FROM camshoter_devices WHERE ID='$id'");
  // some action for related tables
  SQLExec("DELETE FROM camshoter_devices WHERE ID='".$rec['ID']."'");
 }
/**
* InData edit/add
*
* @access public
*/
 function editdevices(&$out, $id) {	
  require(DIR_MODULES.$this->name.'/indata_edit.inc.php');
 } 

//////////////////////////////////////////////

/**

*
* @access public
*/
 
/**

*
* @access public
*/
 
/**
* Install
*
* Module installation routine
*
* @access private
*/
 function install($data='') {

  parent::install();
 }
/**
* Uninstall
*
* Module uninstall routine
*
* @access public
*/
 function uninstall() {

SQLExec('DROP TABLE IF EXISTS camshoter_devices');
SQLExec('DROP TABLE IF EXISTS camshoter_config');
  parent::uninstall();

 }
/**
* dbInstall
*
* Database installation routine
*
* @access private
*/
 function dbInstall($data = '') {

  $data = <<<EOD
 camshoter_devices: ID int(10) unsigned NOT NULL auto_increment
 camshoter_devices: TITLE varchar(100) NOT NULL DEFAULT ''
 camshoter_devices: IPADDR varchar(100) NOT NULL DEFAULT ''
 camshoter_devices: ONLINE varchar(100) NOT NULL DEFAULT ''
 camshoter_devices: TS varchar(100) NOT NULL DEFAULT ''
 camshoter_devices: SROK varchar(100) NOT NULL DEFAULT ''
 camshoter_devices: TYPE varchar(100) NOT NULL DEFAULT ''
 camshoter_devices: TS varchar(100) NOT NULL DEFAULT ''
 camshoter_devices: URL varchar(100) NOT NULL DEFAULT ''
 camshoter_devices: METHOD varchar(100) NOT NULL DEFAULT ''
 camshoter_devices: SENDTELEGRAM int(1) 
 camshoter_devices: SENDEMAIL int(1) 
 camshoter_devices: LASTPING datetime
 camshoter_devices: UPDATED datetime
 camshoter_devices: LINKED_OBJECT varchar(255) NOT NULL DEFAULT ''
 camshoter_devices: LINKED_PROPERTY varchar(255) NOT NULL DEFAULT ''



EOD;
  parent::dbInstall($data);

  $data = <<<EOD
 camshoter_config: parametr varchar(300)
 camshoter_config: value varchar(10000)  
EOD;
   parent::dbInstall($data);



$cmd_rec = SQLSelect("SELECT * FROM camshoter_config");
if ($cmd_rec[0]['EVERY']) {
null;
} else {

$par['parametr'] = 'EVERY';
$par['value'] = 30;		 
SQLInsert('camshoter_config', $par);				
	
$par['parametr'] = 'LASTCYCLE_TS';
$par['value'] = "0";		 
SQLInsert('camshoter_config', $par);						

$par['parametr'] = 'CURRENT';
$par['value'] = "";		 
SQLInsert('camshoter_config', $par);						
		
$par['parametr'] = 'LASTCYCLE_TXT';
$par['value'] = "0";		 
SQLInsert('camshoter_config', $par);						
$par['parametr'] = 'DEBUG';
$par['value'] = "";		 
SQLInsert('camshoter_config', $par);	
}
}


//////////////////////////////////////////////
//////////////////////////////////////////////
	





}
// --------------------------------------------------------------------
	
/*
*
* TW9kdWxlIGNyZWF0ZWQgSmFuIDAzLCAyMDE4IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/


//////////////////////////////////////////////
//////////////////////////////////////////////
