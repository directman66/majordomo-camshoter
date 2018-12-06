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



//$out['arcdate']=date('Ymd');

/*
$start = strtotime('09/01/2010');
$finish = strtotime('05/31/2011');

$arrayOfDates = array();
  for($i=$start; $i<$finish; $i+=86400){
  list($year,$month,$day) = explode("|",date("Y|m|d",$i));
  $arrayOfDates[$year][$month][] = $day;
}
*/
//print_r(getDatesFromRange( '2010-10-01', '2010-10-05' ));

//print_r($this->createDateRangeArray('2010-10-01', '2010-10-05'));



	




 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }


$this->searchdevices($out, $this->id);




 if ($this->view_mode=='indata_edit') {
   $this->editdevices($out, $this->id);




 }


 if ($this->view_mode=='updatesize') {
$this->getfoldersize($this->id);
   $this->redirect("?");
}


 if ($this->view_mode=='home') {
   $this->redirect("?");
}




 if ($this->view_mode=='indata_del') {
   $this->delete($this->id);
//   $this->redirect("?data_source=$this->data_source&view_mode=node_edit&id=$pid&tab=indata");
   $this->redirect("?");
 }	


 if ($this->view_mode=='clearfolder') {
   $this->clearpath($this->id);
//   $this->redirect("?data_source=$this->data_source&view_mode=node_edit&id=$pid&tab=indata");
   $this->redirect("?");
 }	




}

  




 



 function clearpath($id) {

$savepath=ROOT."cms/cached/nvr/cam".$id;
$this->rmRec($savepath);

//$devices=SQLSelect("SELECT * FROM camshoter_devices");

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

$savepath=ROOT."cms/cached/nvr/cam".$properties[$i]['ID'].'/'.date('Y-m-d').'/';
 if (!file_exists($savepath)) {
mkdir($savepath, 0777, true);}
$savelast=ROOT."cms/cached/nvr/last/";
 if (!file_exists($savelast)) {
mkdir($savelast, 0777, true);}




if ($properties[$i]['TYPE']=='snapshot')
{
$iam='img';
$image_url=$properties[$i]['URL'];
$savename=$savepath."cam".$properties[$i]['ID']."_".date('Y-m-d_His').".jpg"; // куда сохранять
$savenamelast=$savelast."cam".$properties[$i]['ID'].".jpg"; // куда сохранять

$result=getURL($image_url,0);
SaveFile($savename, $result);
SaveFile($savenamelast, $result);
}
/*
if (($properties[$i]['TYPE']=='rtsp')&&($properties[$i]['METHOD']=='mov'))
{
$iam='video';
$url=$properties[$i]['URL'];
$sec=$properties[$i]['SEC'];
$savename=$savepath."cam".$properties[$i]['ID']."_".date('Y-m-d_His').".mov"; // куда сохранять
$savenamelast=$savelast."cam".$properties[$i]['ID'].".jpg"; // куда сохранять

//windows
//exec('C:\_majordomo\apps\ffmpeg\ffmpeg.exe -y -i rtsp://192.168.2.89:554/12 -t 5 -f mp4 -vcodec libx264 -pix_fmt yuv420p -an -vf scale=w=640:h=480:force_original_aspect_ratio=decrease -r 15 C:/_majordomo/htdocs/cached/img/out.mp4'); 
//linux
exec('ffmpeg -y -i "'.$url.'" -t '.$sec.' -f mp4 -mov  -an -r 15 '.$savename); 
exec('ffmpeg -y -i "'.$url.'"  -f image2  -updatefirst 1 '.$savenamelast); 
}
*/

if (($properties[$i]['TYPE']=='rtsp')&&($properties[$i]['METHOD']=='mp4'))
{
$iam='video';
$url=$properties[$i]['URL'];
$sec=$properties[$i]['SEC'];
$savename=$savepath."cam".$properties[$i]['ID']."_".date('Y-m-d_His').".mp4"; // куда сохранять
$savenamethumb=$savepath."cam".$properties[$i]['ID']."_".date('Y-m-d_His').".jpg"; // куда сохранять
$savenamelast=$savelast."cam".$properties[$i]['ID'].".jpg"; // куда сохранять

//linux

if (substr(php_uname(),0,5)=='Linux')  {
exec('timeout -s INT 60s ffmpeg -y -i "'.$url.'" -t '.$sec.' -f mp4 -vcodec libx264 -pix_fmt yuv420p -an -r 15 '.$savename); 
//exec('timeout -s INT 60s ffmpeg -y -i "'.$url.'"  -f image2  -updatefirst 1 '.$savenamethumb); 
//exec('timeout -s INT 60s ffmpeg -y -i "'.$savename.'"  -f image2  -updatefirst 1 '.$savenamethumb); 
//http://digilinux.ru/2010/10/21/how-to-split-frames-with-ffmpeg/
exec('timeout -s INT 60s ffmpeg -y -i "'.$savename.'"  -r 1 -t 00:00:01 -f image2  -updatefirst 1 '.$savenamethumb); 


}
else 
{
//windows
//exec('C:\_majordomo\apps\ffmpeg\ffmpeg.exe -y -i rtsp://192.168.2.89:554/12 -t 5 -f mp4 -vcodec libx264 -pix_fmt yuv420p -an -vf scale=w=640:h=480:force_original_aspect_ratio=decrease -r 15 C:/_majordomo/htdocs/cached/img/out.mp4'); 

exec('C:\_majordomo\apps\ffmpeg\ffmpeg.exe -y -i "'.$url.'" -t '.$sec.' -f mp4 -vcodec libx264 -pix_fmt yuv420p -an -r 15 '.$savename); 
//exec('C:\_majordomo\apps\ffmpeg\ffmpeg.exe -y -i "'.$url.'"  -f image2  -updatefirst 1 '.$savenamethumb); 
exec('C:\_majordomo\apps\ffmpeg\ffmpeg.exe -y -i "'.$savename.'"  -r 1 -t 00:00:01 -f image2  -updatefirst 1 '.$savenamethumb); 
}

copy($savenamethumb, $savenamelast);



}

///отправка в телеграм
if ((SQLSELECTONE("CHECK TABLE tlg_cmd")['Msg_text']=='OK')&&
($properties[$i]['SENDTELEGRAM']=1))

 {	 
$fsize=filesize($savename);
$text='Зафиксировано движение '.$properties[$i]['TITLE'];
include_once(DIR_MODULES . 'telegram/telegram.class.php');
$telegram_module = new telegram();
if ($iam=='img') {$telegram_module->sendImageToAll($savename,$text);}
if (($iam=='video')&&($fsize>500)) {$telegram_module->sendVideoToAll($savename,$text);}

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
 camshoter_devices: URL varchar(100) NOT NULL DEFAULT ''
 camshoter_devices: METHOD varchar(100) NOT NULL DEFAULT ''
 camshoter_devices: SENDTELEGRAM int(1) 
 camshoter_devices: SENDEMAIL int(1) 
 camshoter_devices: SENDSLAKS int(1) 
 camshoter_devices: SEC int(1) 
 camshoter_devices: COUNT int(10) 
 camshoter_devices: SIZE varchar(100) NOT NULL DEFAULT ''
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
	




function rmRec($dir) {
  $d=opendir($dir);  
    while(($entry=readdir($d))!==false) 
    { 
        if ($entry != "." && $entry != "..") 
        { 
            if (is_dir($dir."/".$entry)) 
            {  
                dirDel($dir."/".$entry);  
            } 
            else 
            {  
                unlink ($dir."/".$entry);  
            } 
        } 
    } 
    closedir($d);  
    rmdir ($dir);  

  }


function createDateRangeArray($strDateFrom,$strDateTo)
{
    // takes two dates formatted as YYYY-MM-DD and creates an
    // inclusive array of the dates between the from and to dates.

    // could test validity of dates here but I'm already doing
    // that in the main script
    $i=0;
    $aryRange=array();

//$aryRange[] = array("DATA"=>"");

    $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
    $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

    if ($iDateTo>=$iDateFrom)
    {
//        array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
//        array_push($aryRange["DATA"],date('Y-m-d',$iDateFrom)); // first entry
$aryRange[] = array("DATE"=>date('Y-m-d',$iDateFrom), 'KEY=>'.$i);
$i=$i+1;

//$array[] = $var;

        while ($iDateFrom<$iDateTo)
        {
            $iDateFrom+=86400; // add 24 hours
//            array_push($aryRange,date('Y-m-d',$iDateFrom));
//            array_push($aryRange["DATA"],date('Y-m-d',$iDateFrom));
//$aryRange[] = array("DATE"=>date('Y-m-d',$iDateFrom));
$aryRange[] = array("DATE"=>date('Y-m-d',$iDateFrom), 'KEY=>'.$i);
$i=$i+1;

        }
    }
$aryRange=array_reverse($aryRange);

    return $aryRange;
}



///////список файлов папки
function getfiles($dir) {

 $files = array();

 foreach (scandir($dir) as $v) 

{
$upfoler=explode('/',$dir)[7];
$upfoler1=explode('/',$dir)[6];
//$files[] =array("FILE"=>$dir."/".filemtime("$dir/$v"));
//$files[] =array("FILE"=>$dir."/".$v);
if (($v<>"")&&($v<>".")&&($v<>"..")&&(strpos($v,'jpg')>0)
//&&(strpos($v,$fdate)>0
)


{
$files[] =array("FILE"=>$upfoler1."/".$upfoler."/".$v,"FILEMP4"=>$upfoler1."/".$upfoler."/".substr($v,0,-3).'mp4' );
}
}
return $files;

}


function getfoldersize($id) {
  $rec=SQLSelectOne("SELECT * FROM camshoter_devices WHERE ID='$id'");
 $folder=ROOT."cms/cached/nvr/cam".$id.'/';

// $cnt=count(scandir('/'.$folder.'/'));

$cnt = $this->DirFilesR($folder);


$dirsize=$this->show_size($folder);

$rec['COUNT']=$cnt;
$rec['SIZE']=$dirsize;

SQLUpdate('camshoter_devices', $rec);


}


function show_size($f,$format=true) 
{ 
        if($format) 
        { 
                $size=$this->show_size($f,false); 
                if($size<=1024) return $size.' bytes'; 
                else if($size<=1024*1024) return round($size/(1024),2).' Kb'; 
                else if($size<=1024*1024*1024) return round($size/(1024*1024),2).' Mb'; 
                else if($size<=1024*1024*1024*1024) return round($size/(1024*1024*1024),2).' Gb'; 
                else if($size<=1024*1024*1024*1024*1024) return round($size/(1024*1024*1024*1024),2).' Tb'; //:))) 
                else return round($size/(1024*1024*1024*1024*1024),2).' Pb'; // ;-) 
        }else 
        { 
                if(is_file($f)) return filesize($f); 
                $size=0; 
                $dh=opendir($f); 
                while(($file=readdir($dh))!==false) 
                { 
                        if($file=='.' || $file=='..') continue; 
                        if(is_file($f.'/'.$file)) $size+=filesize($f.'/'.$file); 
                        else $size+=$this->show_size($f.'/'.$file,false); 
                } 
                closedir($dh); 
                return $size+filesize($f); // +filesize($f) for *nix directories 
        } 
} 





//получение названий файлов
function DirFilesR($dir)  
{  
  $handle = opendir($dir) or die("Can't open directory $dir");  
  $files = Array();  
  $subfiles = Array();  
  while (false !== ($file = readdir($handle)))  
  {  
    if ($file != "." && $file != "..")  
    {  
      if(is_dir($dir."/".$file))  
      {  
          // Получим список файлов  

          // вложенной папки...  

        $subfiles = $this->DirFilesR($dir."/".$file);  

          // ...и добавим их к общему списку  

        $files = array_merge($files,$subfiles);  
      }  
      else 
      {  
        $files[] = $dir."/".$file;  
      }  
    }  
  }  
  closedir($handle);  
//  print_r($files);
  return count($files);  
  }



}
// --------------------------------------------------------------------
	
/*
*
* TW9kdWxlIGNyZWF0ZWQgSmFuIDAzLCAyMDE4IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/


//////////////////////////////////////////////
//////////////////////////////////////////////
