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
  $this->title="CAMshoter";
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
  global $file;
  global $teg;
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


global $sizethmb;
if (!$sizethmb) $sizethmb=200;
$out['SIZETHMB1']=$sizethmb;


$cmd_rec = SQLSelectOne("SELECT * FROM camshoter_config where parametr='SIZEALL'");
$out['SIZEALL']=$cmd_rec['value'];






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


 if ($this->tab=='settings') {
$cmd_rec = SQLSelectOne("SELECT * FROM camshoter_config where parametr='VISION_TOKEN'");
$out['VISION_TOKEN']=$cmd_rec['value'];
}


 if ($this->tab=='users') {



$gfolder=ROOT."cms/cached/nvr/users/";
$files=$this->getusers($gfolder);
//print_r($files);
//jpeg
$out['FILES']=$files;

}


 if ($this->tab=='faces') {
$gfolder=ROOT."cms/cached/nvr/faces/";
$files=$this->getusers($gfolder);
//print_r($files);
//jpeg
$out['FILES']=$files;

}


 if ($this->tab=='preview') {
$gfolder=ROOT."cms/cached/nvr/last/";
$files=$this->getusers($gfolder);
//print_r($files);
//jpeg
$out['FILES']=$files;

}




 if ($this->view_mode=='indata_edit') {
   $this->editdevices($out, $this->id);
 }

//echo "mode ".$this->mode."::".$this->tab;
//echo "<br>";
//echo "wiewmode ".$this->viewmode."::".$this->tab;

 if ($this->view_mode=='adduser') {
   $this->adduser($out, $this->id);
   $this->redirect("?tab=users");
 }


 if ($this->view_mode=='copyfav') {
$file1=ROOT.$this->id;
$file2=ROOT."cms/cached/nvr/users/".explode("/",$this->id)[5];
echo "file1 ".$file1;
echo "<br>";
echo "file2 ".$file2;

copy($file1,$file2);
$this->redirect("?tab=users");
 }




 if ($this->view_mode=='obuchit') {
   $this->vision_setface($this->id);
   $this->redirect("?tab=users");
 }



 if ($this->view_mode=='deluserfile') {
$fn=ROOT.'cms/cached/nvr/'.$this->id;
echo $fn;
unlink($fn);

$this->redirect("?tab=users");
 }




 if ($this->view_mode=='saveuser') {

 global $filename;

$filename=explode("/",$filename)[1];
//echo $filename;
  $rec=SQLSelectOne("SELECT * FROM camshoter_people WHERE FILENAME='$filename'");

//  $rec=SQLSelectOne("SELECT * FROM $table_name WHERE ID='$id'");
 global $selecteduser;
echo $selecteduser;
   $rec['PEOPLENAME']=sqlselectone('select * from users where ID='.$selecteduser)['NAME'];
//   $rec['PEOPLENAME']='123';
   $rec['USERID']=$selecteduser;
   sqlupdate('camshoter_people', $rec);
   $this->redirect("?tab=users");
 }


////////////////
 if ($this->mode=='confirm') {
// $this->redirect("?view_mode=indata_edit&tab=devcount&id=".$this->id);
 }









  if (($this->mode=='update')&&($this->tab=='settings')) {
//  $rec=SQLSelectOne("SELECT * FROM $table_name WHERE ID='$id'");
 
   global $vision_token;
 //  $rec['TITLE']=$vision_token;

$cmd_rec = SQLSelectOne("SELECT * FROM camshoter_config where parametr='VISION_TOKEN'");

if ($cmd_rec['value']) 
{
//echo "1";
//SQLUpdate('camshoter_config', cmd_rec);						
SQLExec("update camshoter_config set value='".$vision_token."' where parametr='VISION_TOKEN'");
}
else
{
//echo "2";
$cmd_rec['parametr'] = 'VISION_TOKEN';
$cmd_rec['value'] = $vision_token;		 
SQLInsert('camshoter_config', $cmd_rec);	
}
 $this->redirect("?tab=settings");

}


 if ($this->view_mode=='updatesize') {

$this->getfoldersize($this->id);
   $this->redirect("?");
}



 if ($this->view_mode=='updatesizeall') {

$this->getsizeall();


}




 if ($this->view_mode=='detect') {


$file=ROOT.$this->id;
$idd=substr(explode("/",$this->id)[4],3);
echo $file;

if  (strpos($file,'..')>0)

 {$file=ROOT.substr($file,3);}
//sg('test.mjmk',$file);
//sg('test.mkjidd',$idd);
//sg('test.mkjteg',$this->teg);

$this->mailvision_detect($file, $idd);
$this->mailvision_detect_face($file, $idd);
$this->redirect("?tab=devcount");
}



 if ($this->view_mode=='home') {
   $this->redirect("?");
}


 if ($this->view_mode=='sendaction') {
setglobal($this->id, '1');
//callmethod($this->id.'motionDetected');
$this->redirect("?action=camshoter");
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

  



 function getsizeall() {

$cmd=sqlselect('select * from camshoter_devices');
$total = count($cmd);
for ($i = 0; $i < $total; $i++)
{
//$folder=ROOT."cms/cached/nvr/cam".$cmd[$i]['ID'].'/';
//echo $folder;
$this->getfoldersize($cmd[$i]['ID']);
}

$allsize=$this->show_size(ROOT."cms/cached/nvr/");

$cmd=SQLSelectOne("select * from  camshoter_config where parametr='SIZEALL'");
$cmd['value']= $allsize;
$cmd['parametr']= 'SIZEALL';
if ($cmd['ID'])
{
sqlupdate('camshoter_config',$cmd);}
else 
{
sqlinsert('camshoter_config',$cmd);}


$this->redirect("?"); 
}


 function clearpath($id) {

$savepath=ROOT."cms/cached/nvr/cam".$id.'/';
//$this->rmRec($savepath);
echo $savepath;
$this->delFolder($savepath);



//$devices=SQLSelect("SELECT * FROM camshoter_devices");

 }



 function adduser(&$out, $id) {
echo "1";
  require(DIR_MODULES.$this->name.'/adduser.inc.php');
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
$lastping=$mhdevices[$i]['LASTPING2'];
if ((!$lastping)||(time()-$lastping>300))
//echo time()-$lastping;
//if (time()-$lastping>300)
 {

$cmd='
$online=ping(processTitle("'.$ip.'"));
if ($online) 
{SQLexec("update camshoter_devices set ONLINE=1, LASTPING2='.time().' where IPADDR=\''.$ip.'\'");} 
else 
{SQLexec("update camshoter_devices set ONLINE=0, LASTPING2='.time().' where IPADDR=\''.$ip.'\'");}

';
 SetTimeOut('camshoter_devices_ping'.$i,$cmd, '1'); 
debmes($cmd, 'camshoter');

/*

$online=ping(processTitle($ip));
    if ($online) 
{SQLexec("update camshoter_devices set ONLINE='1', LASTPING=".time()." where IPADDR='$ip'");} 
else 
{SQLexec("update camshoter_devices set ONLINE='0', LASTPING=".time()." where IPADDR='$ip'");}
*/
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
 if ($this->owner->action=='apps') {
  $this->redirect(ROOTHTML."module/".$this->name.".html");
 } else 
 $this->admin($out);
}


 function propertySetHandle($object, $property, $value) {
//   sg('test.camshoter','123');

   $table='camshoter_devices';
$sql="SELECT * FROM $table WHERE LINKED_OBJECT LIKE '".DBSafe($object)."' AND LINKED_PROPERTY LIKE '".DBSafe($property)."'";
   $properties=SQLSelect($sql);
//   sg('test.camshoter',  $sql);
//   sg('test.camshoter',  $properties['TYPE']);


//никого дома нет статус
$nobodyactive=gg('NobodyHomeMode.active');

   $total=count($properties);
   if ($total) {
    for($i=0;$i<$total;$i++) {

$body=1;

if (($properties[$i]['SOMEBODYIGNORE']=='1')&& ($nobodyactive==0))
{$body=0;} 
else 
{$body=1;} 
	 if( ($properties[$i]['ID'])&&($properties[$i]['ENABLE']==1)&&($body==1)) {
//		sg($properties[$i]['TARGET_OBJECT'].'.'.$properties[$i]['TARGET_PROPERTY'], (int)!$value);
//	 } else { 
//		sg($properties[$i]['TARGET_OBJECT'].'.'.$properties[$i]['TARGET_PROPERTY'], $value);


//для теста вызовем нужный метод датчика движения
//cm('Motion11.motionDetected');

debmes('Сработал датчик движения на камере '.$properties[$i]['ID'],'camshoter');

$savepath=ROOT."cms/cached/nvr/cam".$properties[$i]['ID'].'/'.date('Y-m-d').'/';
 if (!file_exists($savepath)) {
mkdir($savepath, 0777, true);}
$savelast=ROOT."cms/cached/nvr/last/";


$users=ROOT."cms/cached/nvr/users/";
if (!file_exists($users)) {
mkdir($users, 0777, true);}



$savefacesdir=ROOT."cms/cached/nvr/faces/";
if (!file_exists($savefacesdir)) {
mkdir($savefacesdir, 0777, true);}







 if (!file_exists($savelast)) {
mkdir($savelast, 0777, true);}



if ($properties[$i]['TYPE']=='snapshot')
{
$iam='img';
$image_url=$properties[$i]['URL'];
$savename=$savepath."cam".$properties[$i]['ID']."_".date('Y-m-d_His').".jpg"; // куда сохранять
$savenamelast=$savelast."cam".$properties[$i]['ID'].".jpg"; // куда сохранять
$savenamethumb=$savename;

$result=getURL($image_url,0);

if ($result) {
SaveFile($savename, $result);
SaveFile($savenamelast, $result);

}else {
$result=file_get_contents($url); //скачиваем картинку с камеры 
file_put_contents($savename, $result);
file_put_contents($savenamelast, $result);
}




}
if (($properties[$i]['TYPE']=='rtsp')&&($properties[$i]['METHOD']=='mp4'))
{
$iam='video';
$url=$properties[$i]['URL'];
$sec=$properties[$i]['SEC'];
$savename=$savepath."cam".$properties[$i]['ID']."_".date('Y-m-d_His').".mp4"; // куда сохранять
$savenamethumb=$savepath."cam".$properties[$i]['ID']."_".date('Y-m-d_His').".jpg"; // куда сохранять
$savenamethumbdir=$savepath."cam".$properties[$i]['ID']."_".date('Y-m-d_His'); // куда сохранять
$savenamelast=$savelast."cam".$properties[$i]['ID'].".jpg"; // куда сохранять
$savenameface=$savefacesdir."cam".$properties[$i]['ID']."_".date('Y-m-d_His').".jpg"; // куда сохранять


$savenamethumbdir=ROOT."cms/cached/nvr/cam".$properties[$i]['ID'].'/'.date('Y-m-d').'/'."cam".$properties[$i]['ID']."_".date('Y-m-d_His')."/";
 if (!file_exists($savenamethumbdir)) {
mkdir($savenamethumbdir, 0777, true);}



//linux
if (substr(php_uname(),0,5)=='Linux')  {
//exec('timeout -s INT 60s ffmpeg -y -i "'.$url.'" -t '.$sec.' -f mp4 -vcodec libx264 -pix_fmt yuv420p -an -r 15 '.$savename); 
//  exec('timeout -s INT 60s ffmpeg -y -i "'.$url.'" -t '.$sec.' -f mp4 -vcodec copy -pix_fmt yuv420p -acodec ac3 -an -r 15 '.$savename); 
//  exec('timeout -s INT 60s ffmpeg -y -i "'.$url.'" -t '.$sec.' -f mp4 -vcodec copy -pix_fmt yuv420p -acodec pcm_s16le -an -r 15 '.$savename); 
if  ($properties[$i]['FFMPEGCMD']=="")
{
//  exec('timeout -s INT 60s ffmpeg -y -i "'.$url.'" -t '.$sec.' -f mp4 -vcodec copy -pix_fmt yuv420p -acodec copy -an -r 15 '.$savename); 
//  exec('ffmpeg -y -i "'.$url.'" -t '.$sec.' -f mp4 -vcodec copy -pix_fmt yuv420p -acodec copy -an -r 15 '.' 2>&1'.$savename); 
$cmd='ffmpeg -y -i "'.$url.'" -t '.$sec.' -f mp4 -vcodec copy -pix_fmt yuv420p -acodec copy -an -r 15 '.$savename;
$res = exec($cmd . ' 2>&1', $output);





 } else
{
$cmd='timeout -s INT 60s '.str_replace('#savename',$savename, str_replace('#sec',$sec, $properties[$i]['FFMPEGCMD']));
exec($cmd); 
}
debmes('Видео сохранено  '.$savename,'camshoter');


//-vcodec copy -b 64k -acodec ac3

//exec('timeout -s INT 60s ffmpeg -y -i "'.$url.'"  -f image2  -updatefirst 1 '.$savenamethumb); 
//exec('timeout -s INT 60s ffmpeg -y -i "'.$savename.'"  -f image2  -updatefirst 1 '.$savenamethumb); 
//http://digilinux.ru/2010/10/21/how-to-split-frames-with-ffmpeg/

// первый кадр на обложку
exec('timeout -s INT 60s ffmpeg -y -i "'.$savename.'"  -r 1 -t 00:00:01 -f image2  -updatefirst 1 '.$savenamethumb); 

//раскадровка каждый 4 кадр в отдельную папку для определения наличия лиц
//exec('timeout -s INT 120s ffmpeg -y -i "'.$savename.'"  -r 0.25 -ss 00:00:00 -t 00:00:10 -f image2   '.$savenamethumbdir.'frames_%04d.png'); 
exec('timeout -s INT 120s ffmpeg -y -i "'.$savename.'"  -r 0.25  -f image2   '.$savenamethumbdir.'frames_%04d.jpg'); 
debmes('Раскадровка сохранена  '.$savenamethumbdir,'camshoter');

}
else 
{
//windows
exec('C:\_majordomo\apps\ffmpeg\ffmpeg.exe -y -i "'.$url.'" -t '.$sec.' -f mp4 -vcodec libx264 -pix_fmt yuv420p -an -r 15 '.$savename); 
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


$ts=$properties[$i]['TELEGRAMUSERS'];

debmes('telegram users  '.$ts,'camshoter');

$tsar=explode(',', $ts);

debmes($tsar,'camshoter');


$total=count($tsar);
debmes('total  '.$total,'camshoter');
for ($i = 0; $i < $total; $i++)
{



$user=SQLSElectOne("select * from tlg_user where ID='".$tsar[$i]."'")['USER_ID'];

//if ($iam=='img') {$telegram_module->sendImageToAll($savename,$text);}
//if (($iam=='video')&&($fsize>500)) {$telegram_module->sendVideoToAll($savename,$text);}


if ($iam=='img') {$telegram_module->sendImageToUser($user,$savename,$text);}
if (($iam=='video')&&($fsize>500)) {$telegram_module->sendVideoToUser($user,$savename,$text);}



debmes('Файл '.$savename .' отправлен в телегу пользователю '.$user,'camshoter');
}
}
	 $properties[$i]['UPDATED']=date('Y-m-d H:i:s');
	 SQLUpdate('camshoter_devices', $properties[$i]);



//..if ($iam=='img') {$detect$this->mailvision_detect($savename);}
//if (($iam=='video')&&($fsize>500)) {$detect$this->mailvision_detect($savename);}


//определяем, есть ли на фото лицо

$fddpath=DIR_MODULES.$this->name."/FaceDetector.php";
debmes('Запускаем процесс facedetect '.$fddpath,'camshoter');
error_reporting(0);
//include "FaceDetector.php";

//require(DIR_MODULES.$this->name.'/FaceDetector.php');
include $fddpath;

$face_detect = new Face_Detector('detection.dat');
debmes('facedetect открыт','camshoter');


//проверяем  первый кадр, вдруг мы работает только со снапшотами
//if ($face_detect->face_detect($savenamethumb)) 
//{$face=1;
//$face_detect->cropsave($savenameface);
//$face_detect->cropFace2('new.jpg');
//} else $face=0; 
//echo $face;

//debmes('Идем по кадрам '.$savenamethumbdir,'camshoter');
//распознаем, если лицо
//if ($face==1) $this->mailvision_detect_face($savenameface, $id);


///идем по кадрам, сохраненным из видео
debmes('Идем по кадрам '.$savenamethumbdir,'camshoter');

if (($savenamethumbdir)&&($savenamethumbdir<>"")&& (is_dir($savenamethumbdir))){
 foreach (scandir($savenamethumbdir) as $v) 
{
debmes('Проверяем фото '.$savenamethumbdir.$v.' на наличие лиц','camshoter');
if ($face_detect->face_detect($savenamethumbdir.$v)) 
{$face=1;
debmes('В кадре лицо!!  '.$savenamethumbdir.$v,'camshoter');
$face_detect->cropsave($savenameface);
//$face_detect->cropFace2('new.jpg');
} else $face=0; 
//echo $face;

//if ($face==1) $this->mailvision_detect_face($savenameface, $id);
}}



$this->mailvision_detect($savenamethumb, $id);




//$this->mailvision_detect($savenamelast);
	}
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

	function processSubscription($event_name, $details='') {
		if ($event_name=='HOURLY') {

$this->getsizeall();
}

}



 function install($data='') {
subscribeToEvent($this->name, 'HOURLY');
$users=ROOT."cms/cached/nvr/users/";
if (!file_exists($users)) {
mkdir($users, 0777, true);}


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
unsubscribeFromEvent($this->name, 'HOURLY');
SQLExec('DROP TABLE IF EXISTS camshoter_devices');
SQLExec('DROP TABLE IF EXISTS camshoter_config');
SQLExec('DROP TABLE IF EXISTS camshoter_recognize');
SQLExec('DROP TABLE IF EXISTS camshoter_people');


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
 camshoter_devices: FFMPEGCMD varchar(300) NOT NULL DEFAULT ''
 camshoter_devices: METHOD varchar(100) NOT NULL DEFAULT ''
 camshoter_devices: SOMEBODYIGNORE int(1) 
 camshoter_devices: SENDTELEGRAM int(1) 
 camshoter_devices: TELEGRAMUSERS varchar(100) NOT NULL DEFAULT ''
 camshoter_devices: SENDEMAIL int(1) 
 camshoter_devices: SENDSLAKS int(1) 
 camshoter_devices: SEC int(1) 
 camshoter_devices: ENABLE int(1) 
 camshoter_devices: COUNT int(10) 
 camshoter_devices: SIZE varchar(100) NOT NULL DEFAULT ''
 camshoter_devices: LASTPING datetime
 camshoter_devices: LASTPING2 varchar(100) NOT NULL DEFAULT ''
 camshoter_devices: UPDATED datetime
 camshoter_devices: LINKED_OBJECT varchar(255) NOT NULL DEFAULT ''
 camshoter_devices: LINKED_PROPERTY varchar(255) NOT NULL DEFAULT ''

EOD;
  parent::dbInstall($data);

  $data = <<<EOD
 camshoter_config: ID int(10) unsigned NOT NULL auto_increment
 camshoter_config: parametr varchar(300)
 camshoter_config: value varchar(10000)  
EOD;
   parent::dbInstall($data);



  $data = <<<EOD
 camshoter_recognize: ID int(10) unsigned NOT NULL auto_increment
 camshoter_recognize: CAMID int(1) 
 camshoter_recognize: UPDATED datetime
 camshoter_recognize: FILENAME varchar(100) NOT NULL DEFAULT ''
 camshoter_recognize: ANSWER varchar(1000) NOT NULL DEFAULT ''
 camshoter_recognize: FACES varchar(1000) NOT NULL DEFAULT ''

EOD;
   parent::dbInstall($data);


  $data = <<<EOD
 camshoter_people: ID int(10) unsigned NOT NULL auto_increment
 camshoter_people: UPDATED datetime
 camshoter_people: FILENAME varchar(100) NOT NULL DEFAULT ''
 camshoter_people: PEOPLENAME varchar(100) NOT NULL DEFAULT ''
 camshoter_people: ANSWER varchar(1000) NOT NULL DEFAULT ''
 camshoter_people: USERID int(3) 


EOD;
   parent::dbInstall($data);



/*
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
*/
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


/* if (substr(php_uname(),0,5)=='Linux')  {

$cnt=substr_count(

$dir=str_replace(chr(92),"/",$dir);
$upfoler=explode('/',$dir)[7];
$upfoler1=explode('/',$dir)[6];
}
else 
{
$dir=str_replace('/',chr(92),$dir);    
$upfoler=explode(chr(92),$dir)[7];
$upfoler1=explode(chr(92),$dir)[6];

}
*/

$cnt=substr_count( $dir, '/');
//$upfoler=explode(chr(92),$dir)[$cnt-1];
//$upfoler1=explode(chr(92),$dir)[$cnt-2];
$upfoler=explode('/',$dir)[$cnt-1];
$upfoler1=explode('/',$dir)[$cnt-2];



debmes('camshoter storage dir:'.$dir, 'camshoter');
debmes('camshoter storage cnt:'.$cnt, 'camshoter');
debmes('camshoter upfoler:'.$upfoler, 'camshoter');
debmes('camshoter upfoler1:'.$upfoler1, 'camshoter');

 $files = array();

if (($dir)&&($dir<>"")&& (is_dir($dir))){
 foreach (scandir($dir) as $v) 

{
global $sizethmb;
if (!$sizethmb) $sizethmb=200;
if (($v<>"")&&($v<>".")&&($v<>"..")&&(strpos($v,'jpg')>0)
)


{
$sql="select * from camshoter_recognize where filename like '%".substr($v,0,-3)."%'";
$zapr=SqlSelectOne($sql);
$meta1=$zapr['ANSWER'];
if ($meta1){
$meta=$meta1;
$json=$meta1;
$meta2=json_decode($meta1, true);
$meta3=$meta2['body']['object_labels'][0]['labels'];
$meta="";
   foreach ($meta3 as $value)
{$meta.=','.$value['rus'];}

$meta=substr($meta,1);
}

$faces=$zapr['FACES'];


$files[] =array("FILE"=>$upfoler1."/".$upfoler."/".$v,"FILEMP4"=>$upfoler1."/".$upfoler."/".substr($v,0,-3).'mp4','SIZETHMB'=>$sizethmb, 'ID'=>substr($upfoler1,3), 'META'=>$meta, 'JSON'=>$json, 'FACES'=>$faces );
}
}
return $files;
}

}


function delFolder($dir)
{
$files = array_diff(scandir($dir), array('.','..'));
foreach ($files as $file) {
(is_dir("$dir/$file")) ? $this->delFolder("$dir/$file") : unlink("$dir/$file");
}
return rmdir($dir);
}


function getusers($dir) {


  $tempusers=SQLSelect("SELECT ID, NAME FROM users ORDER BY NAME");
  $users_total=count($tempusers);
  for($users_i=0;$users_i<$users_total;$users_i++) {
//   $user_id_opt[$tmp[$users_i]['ID']]=$tmp[$users_i]['NAME'];
//$users[]=$tempusers['NAME'];
  }

$users=$tempusers;

//print_r($users);

/*
 if (substr(php_uname(),0,5)=='Linux')  {
$dir=str_replace(chr(92),"/",$dir);
$upfoler=explode('/',$dir)[7];
$upfoler1=explode('/',$dir)[6];
}
else 
{
$dir=str_replace('/',chr(92),$dir);    
$upfoler=explode(chr(92),$dir)[7];
$upfoler1=explode(chr(92),$dir)[6];
}
*/


$cnt=substr_count( $dir, '/');
//$upfoler=explode(chr(92),$dir)[$cnt-1];
//$upfoler1=explode(chr(92),$dir)[$cnt-2];
$upfoler=explode('/',$dir)[$cnt-1];
$upfoler1=explode('/',$dir)[$cnt-2];


 $files = array();

if (($dir)&&($dir<>"")){
 foreach (scandir($dir) as $v) 


{
$sizethmb=300;

if (($v<>"")&&($v<>".")&&($v<>"..")
//&&(strpos($v,'jpg')>0)
)
{
$sql="select * from camshoter_people where FILENAME='$v'";
$sqlzapr=SQLSelectOne($sql);
$username=$sqlzapr['PEOPLENAME'];
$userid=$sqlzapr['USERID'];
$meta=$sqlzapr['ANSWER'];

///$files[] =array("FILE"=>$upfoler1."/".$upfoler."/".$v,'SIZETHMB'=>$sizethmb, 'ID'=>substr($upfoler1,3));
$files[] =array("FILE"=>$upfoler1."/".$v,'SIZETHMB'=>$sizethmb, 'ID'=>$userid, 'USERS'=>$users, 'USERNAME'=>$username, 'META'=>$meta);


if (!$sqlzapr['ID'])

{
$sqlzapr['FILENAME']=$v;
$sqlzapr['UPDATED']=date('Y-m-d H:i:s');
sqlinsert('camshoter_people', $sqlzapr);
}


}}
}
return $files;


}



	function getfoldersize($id) {
  $rec=SQLSelectOne("SELECT * FROM camshoter_devices WHERE ID='$id'");
 $folder=ROOT."cms/cached/nvr/cam".$id.'/';

// $cnt=count(scandir('/'.$folder.'/'));

$cnt = $this->DirFilesR($folder);


if (is_dir($folder)) { 
$dirsize=$this->show_size($folder);} else {$dirsize=0;}

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



////определение объектов
function mailvision_detect($file, $id)  
{  

//if  (strpos($file,'..')>0) {$file=ROOT.substr($file,3);}

//sg('test.cmsh',$file);


$cmd_rec = SQLSelectOne("SELECT * FROM camshoter_config where parametr='VISION_TOKEN'");
$token=$cmd_rec['value'];




//$cmd_rec = SQLSelectOne("SELECT * FROM camshoter_devices where ID='$id'");
//$id=$cmd_rec['value'];



$cmd_rec2 = SQLSelectOne("SELECT * FROM camshoter_recognize where FILENAME='$file'");



//$url = "https://smarty.mail.ru/api/v1/objects/detect?oauth_provider=mcs&oauth_token=$token";
if (substr(php_uname(),0,5)=='Linux')  {
$cmd=' curl -k -v "https://smarty.mail.ru/api/v1/objects/detect?oauth_provider=mcs&oauth_token='.$token.'" -F file_0=@'.$file.'   -F meta=\'{"mode":["object", "scene"],"images":[{"name":"file_0"}]}'."'";
} else 
{
$cmd='C:\_majordomo\apps\curl.exe -k -v "https://smarty.mail.ru/api/v1/objects/detect?oauth_provider=mcs&oauth_token='.$token.'" -F file_0=@'.$file.'   -F meta=\'{"mode":["object", "scene"],"images":[{"name":"file_0"}]}'."'";
}
$a=shell_exec($cmd); 

$cmd_rec2['ANSWER']=$a;
$cmd_rec2['FILENAME']=$file;
$cmd_rec2['CAMID']=$id;
$cmd_rec2['UPDATED']=date('Y-m-d H:i:s');

echo $a;

if (!$cmd_rec2['ID']) {
SQLInsert('camshoter_recognize',$cmd_rec2); }
else 
{SQLUpdate('camshoter_recognize',$cmd_rec2); }
//return a;

}


////определение лиц
function mailvision_detect_face($file, $id)  
{  

$cmd_rec = SQLSelectOne("SELECT * FROM camshoter_config where parametr='VISION_TOKEN'");
$token=$cmd_rec['value'];


$cmd_rec2 = SQLSelectOne("SELECT * FROM camshoter_recognize where FILENAME='$file'");



if (substr(php_uname(),0,5)=='Linux')  {
$cmd='curl -k -v "https://smarty.mail.ru/api/v1/persons/recognize?oauth_provider=mcs&oauth_token='.$token.'" -F file_0=@'.$file.'   -F meta=\'{"space":"0","images":[{"name":"file_0"}]}'."'";
} else 
{
//$cmd='C:\_majordomo\apps\curl.exe -k -v "https://smarty.mail.ru/api/v1/objects/detect?oauth_provider=mcs&oauth_token='.$token.'" -F file_0=@'.$file.'   -F meta=\'{"mode":["object", "scene"],"images":[{"name":"file_0"}]}'."'";
}
$a=shell_exec($cmd); 

$cmd_rec2['FACES']=$a;
$cmd_rec2['FILENAME']=$file;
$cmd_rec2['CAMID']=$id;
$cmd_rec2['UPDATED']=date('Y-m-d H:i:s');

echo $a;

if (!$cmd_rec2['ID']) {
SQLInsert('camshoter_recognize',$cmd_rec2); }
else 
{SQLUpdate('camshoter_recognize',$cmd_rec2); }
//return a;

}



//получение названий файлов
function DirFilesR($dir)  
{  
if (is_dir($dir)) {
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

if (is_array($subfiles))        $files = array_merge($files,$subfiles);  
      }  
      else 
      {  
        $files[] = $dir."/".$file;  
      }  
    }  
  }  
  closedir($handle);  
//  print_r($files);
}
  return count($files);  
  }



function vision_setface($id)  
{
$cmd_rec = SQLSelectOne("SELECT * FROM camshoter_config where parametr='VISION_TOKEN'");
$token=$cmd_rec['value'];



$cmd=sqlselectOne("select * from camshoter_people where USERID='$id'");

$file=ROOT."cms/cached/nvr/users/".$cmd['FILENAME'];;
$peoplename=$cmd['PEOPLENAME'];
$userid=$cmd['USERID'];

if (substr(php_uname(),0,5)=='Linux')  {

//curl -k -v "https://smarty.mail.ru/api/v1/persons/recognize?oauth_provider=mcs&oauth_token=ххх" -F file_0=@examples/friends1.jpg  -F file_1=@examples/rachel-green.jpg -F meta='{"images":[{"name":"file_1"}, {"name":"file_0"}], "space":"1"}'

//$cmdcurl=' curl -k -v "https://smarty.mail.ru/api/v1/persons/recognize?oauth_provider=mcs&oauth_token='.$token.'" -F file_0=@'.$file.'   -F meta=\'{"name":["'.$peoplename.'"],"images":[{"name":"file_0"}]}'."'";
$cmdcurl=' curl -k -v "https://smarty.mail.ru/api/v1/persons/set?oauth_provider=mcs&oauth_token='.$token.'" -F file_0=@'.$file.'   -F meta=\'{"space":"0","images":[{"name":"file_0","person_id":'.$userid.'}]}'."'";
//echo $cmd."<br>";
} else 
{
//$cmd='C:\_majordomo\apps\curl.exe -k -v "https://smarty.mail.ru/api/v1/objects/detect?oauth_provider=mcs&oauth_token='.$token.'" -F file_0=@'.$file.'   -F meta=\'{"mode":["object", "scene"],"images":[{"name":"file_0"}]}'."'";
}
$a=shell_exec($cmdcurl); 

echo  $a;

	$cmd['ANSWER']=$a;

sqlupdate('camshoter_people',$cmd);

return $a;
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
