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


        if ((time() - gg('cycle_camshoterRun')) < 360*30 ) {
			$out['CYCLERUN'] = 1;
		} else {
			$out['CYCLERUN'] = 0;
		}

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





 if ($this->tab=='log') {
$cmd_rec = SQLSelect("SELECT * FROM camshoter_log order by updated desc");
$out['LOG']=$cmd_rec;
}

 if ($this->tab=='logdevice') {
$id=$this->id;
$cmd_rec = SQLSelect("SELECT * FROM camshoter_log  where camid=$id order by updated desc");
$out['LOG']=$cmd_rec;
}






//$out['er']=$this->owner->action;
//$out['er']='12';
/*
 if (
(($this->tab=='preview')&&($this->owner->action=='camshoter')
||
($this->owner->action=='apps')
&&$this->tab<>'devcount' )
)
*/
//echo  $this->owner->action;
if ($this->tab<>'devcount')
 {
$gfolder=ROOT."cms/cached/nvr/last/";
$files=$this->getusers($gfolder);
//print_r($files);
//jpeg
//debmes($files, 'camshoter');
$out['FILES']=$files;
//$out['CAM']=$files[];
$out['SIZETHMB']='500';
} 
//else

// if ($this->owner->action=='apps')
 



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

 if ($this->tab=='faces')

 {
$gfolder=ROOT."cms/cached/nvr/faces/";
$files=$this->getusers($gfolder);
//print_r($files);
//jpeg
$out['FILES']=$files;

}

 if ($this->tab=='users') {



$gfolder=ROOT."cms/cached/nvr/users/";
$files=$this->getusers($gfolder);
//print_r($files);
//jpeg
$out['FILES']=$files;

}

 if ($this->view_mode=='clearlog') {
//$id=$this->id;

//$tab=$this->tab;
$tab=$_GET['tab'];
$id=$_GET['id'];


if ($tab=='log') $sql='delete from camshoter_log';
if ($tab=='logdevice') $sql='delete from camshoter_log where camid='.$id;

//debmes('TAB:'.$tab, 'camshoter');

//debmes($sql, 'camshoter');
SQLExec ($sql);

   $this->redirect("?");
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
$this->clearsubfolder($this->id);


   $this->redirect("?");
}



 if ($this->view_mode=='updatesizeall') {

$this->getsizeall();
$this->manageallfolders();

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


 if ($this->view_mode=='clearcpu') {
SQLExec ('delete from camshoter_config where parametr="cpu" ');
   $this->redirect("?tab=htop");
}



 if ($this->view_mode=='sendaction') {
setglobal($this->id, '1');
//callmethod($this->id.'motionDetected');
$this->redirect("?action=camshoter");
}



 if ($this->view_mode=='runall') {

$cmd=sqlselect('select * from camshoter_devices  where  enable=1');
$total = count($cmd);
for ($i = 0; $i < $total; $i++)
{

$cmdd='
include_once(DIR_MODULES . "camshoter/camshoter.class.php");
$camshoter= new camshoter();
$cmd=array();

';

foreach ($cmd[$i] as $val=>$name){
//$cmdd.='$cmd[]=array("'.$val.'"=>"'.$name.'");';
$cmdd.='$cmd["'.$val.'"]="'.$name.'";';
}
$cmdd.='
$camshoter->mainprocesss1($cmd,'.  $i.', "runall");
';
SetTimeOut('camshoter_timer '.$i,$cmdd, '0'); 
//debmes(  $cmdd, 'camshoter');
//$this->mainprocesss1($cmd[$i],  $i);

}
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


 function processCycle() {

$cmd=sqlselect('select * from camshoter_devices  where enable=1 and type="snapshot_diff" order by ID');

$total = count($cmd);
for ($i = 0; $i < $total; $i++)
{


$this->mainprocesss1($cmd[$i], $i, "snapshot_diff");


}


}  



 function hourly() {

$cmd=sqlselect('select * from camshoter_devices  where hourly=1 and enable=1 order by ID');
$total = count($cmd);
for ($i = 0; $i < $total; $i++)
{
//$this->mainprocesss1($cmd[$i],  $i);

$cmdd='
include_once(DIR_MODULES . "camshoter/camshoter.class.php");
$camshoter= new camshoter();
$cmd=array();

';
foreach ($cmd[$i] as $val=>$name){
//$cmdd.='$cmd[]=array("'.$val.'"=>"'.$name.'");';
$cmdd.='$cmd["'.$val.'"]="'.$name.'";';
}
$cmdd.='
$camshoter->mainprocesss1($cmd,'.  $i.', "hourly");
';
SetTimeOut('camshoter_timer '.$i,$cmdd, '0'); 

}

}




 function getsizeall() {

$cmd=sqlselect('select * from camshoter_devices');
$total = count($cmd);
for ($i = 0; $i < $total; $i++)
{
//$folder=ROOT."cms/cached/nvr/cam".$cmd[$i]['ID'].'/';
//echo $folder;
$res=$this->getfoldersize($cmd[$i]['ID']);

}

$logrec=SQLSelectOne('select * from camshoter_log where ID="dummy"');
if (!$logrec['ID'])
{
$localpath=rtrim($localpath,'/');
$logrec['type']='';
$logrec['camid']='';
$logrec['path']='';
$logrec['pathroot']='';
$logrec['message']='';
$logrec['trigger']='getsizeall';
$logrec['updated']=date('Y-m-d H:i:s');
if ($logrec['message']<>'snapshot_diff') SQLInsert('camshoter_log', $logrec);


}

$allsize=$this->show_size(ROOT."cms/cached/nvr/");

$cmd=SQLSelectOne("select * from  camshoter_config where parametr='SIZEALL'");
$cmd['value']= $allsize;
$cmd['parametr']= 'SIZEALL';
if ($cmd['ID'])
{sqlupdate('camshoter_config',$cmd);}
else 
{sqlinsert('camshoter_config',$cmd);}

//return $allsize;
$this->redirect("?"); 
}


 function clearpath($id) {

$savepath=ROOT."cms/cached/nvr/cam".$id.'/';
//$this->rmRec($savepath);
echo $savepath;
$this->delFolder($savepath);



//$devices=SQLSelect("SELECT * FROM camshoter_devices");

 }

 function manageallfolders() {

$rec=SQLSELECT('select * from camshoter_devices');
//debmes($rec, 'camshoter');
$total = count($rec);
for ($i = 0; $i < $total; $i++){
$res=$this->clearsubfolder($rec[$i]['ID']);

/*
$logrec=SQLSelectOne('select * from camshoter_log where ID="dummy"');
if (!$logrec['ID'])
{
$localpath=rtrim($localpath,'/');
$logrec['type']='';
$logrec['camid']=$rec[$i]['ID'];
$logrec['path']='';
$logrec['pathroot']='';
$logrec['message']=$res;
$logrec['trigger']='clearsubfolder';
$logrec['updated']=date('Y-m-d H:i:s');
SQLInsert('camshoter_log', $logrec);
}
*/


}



}



 function clearsubfolder($id) {

$path=ROOT."cms/cached/nvr/cam".$id.'/';
//$this->rmRec($savepath);
//echo $savepath;
//$this->delFolder($savepath);
if (($path)&&($path<>"")){
 foreach (scandir($path) as $v) 


{

if (($v<>"")&&($v<>".")&&($v<>"..")) 
{
//debmes($path.$v, 'campath');
$sec=(time()-strtotime($v));
$day=round((time()-strtotime($v))/86400);
$srok=SQLSElectOne('select * from camshoter_devices where ID='.$id)['SROK'];
//debmes('это папка за дату '.$v.' '.strtotime($v).' урхив устарел на '.$sec.' секунд или '.$day.' дней, а срок хранения '.$srok.' дней', 'campath'); 
if ($day>$srok)  $this->delFolder($path.$v);
}
 foreach (scandir($path.$v) as $vv) {
if (($vv<>"")&&($vv<>".")&&($vv<>"..")&&($v<>"")&&($v<>".")&&($v<>"..")) {

//debmes($path.$v.'/'.$vv, 'campath'); 



}

//if (is_dir($path.$v.'/'.$vv)) {
//$this->delFolder($path.$v.'/'.$vv);
//debmes('deleting '.$path.$v.'/'.$vv, 'campath');
//}
if (is_dir($path.$v.'/'.$vv)&&($vv<>"")&&($vv<>".")&&($vv<>"..")&&($v<>"")&&($v<>".")&&($v<>"..")) 
{
///debmes('deleting '.$path.$v.'/'.$vv, 'campath');
$this->delFolder($path.$v.'/'.$vv);
return 1;
} 
else return 0;

/*else  {
 foreach (scandir($path.$v.'/'.$vv) as $vvv) {
if (($vv<>"")&&($vv<>".")&&($vv<>"..")&&($v<>"")&&($v<>".")&&($v<>"..")&&($vvv<>"")&&($vvv<>".")&&($vvv<>"..")) 

{
debmes($path.$v.'/'.$vv.'/'.$vvv, 'campath');
}

}



}
*/
 }}}}



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
 SetTimeOut('camshoter_devices_ping'.$i,$cmd, '10'); 
//debmes($cmd, 'camshoter');

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

    if ($this->ajax) {
        global $op;
        $result=array();
SQLExec ('delete from camshoter_config where parametr="cpu" and updated<"'.date('Y-m-d').'"');


            if ($op == 'cpu'  ) {
$cpu=SQLSelectOne('select  value from camshoter_config where parametr="cpu"')['value'];
echo ($cpu); 
//echo str_replace('.', ',',$cpu); 
//echo round($cpu); 
}


            if ($op == 'htop'  ) {
//echo "123";      
//echo $fn;      

//print_r(time().": <hr>");
//echo "123";
//$cmd="top  -b -n 1";
//$cmd="top  -b -n 1|grep ffmpeg| grep Cpu";
$cmd2='top  -b -n 1 | grep -E "Cpu|Tasks|Mem|average"';
//$cmd2='top  -b -n 1 ';
$res2 = (shell_exec($cmd2));



//$cmd="top  -b -n 1";


//$cmd="top  -p `pidof -s ffmpeg` -b -n 1";
//$cmd="top -p 'pgrep -f ffmpeg'";

//$cmd='ps -aux | grep -E "ffmpeg|COMMAND"';

//$cmd='ps  --format="%cpu cmd args time" |grep -E "ffmpeg|COMMAND"';
//$cmd='ps  --format="%cpu cmd args" |grep -E "ffmpeg|COMMAND"';

//Доступны следующие форматы: %cpu, %mem, args, c, cmd, comm, cp, cputime, egid, egroup, etime, euid, euser, gid, group, pgid, pgrp, ppid, start, sz, thcount, time, uid, uname и многие другие, ознакомиться с ними в разделе помощи man.

$cmd='ps  --format="%cpu time args" |grep -E "ffmpeg|COMMAND"';

//$cmd='ps -fG ffmpeg';



//$res = exec($cmd , $output);
//$res = (nl2br(shell_exec($cmd)));
$res = (shell_exec($cmd));
/*
echo '
<style>
div{
  text-align:justify;
}

span{  
width:100%;
display:inline-block;
}
</style>';
*/
/*
$sql='select * from camshoter_config where parametr="cpu" and updated>="'.date('Y-m-d').'" limit 100  ' ;

$rec=SQLSelect($sql);
$total=count($rec);
for ($i = 0; $i < $total; $i++)
{
//echo $rec[$i]['value'].",";
$x=$x.$rec[$i]['value'].",";
$y=$y."'".$rec[$i]['updated']."',";
}

$x = substr($x, 0, -1);
$y = substr($y, 0, -1);

echo '
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<div id="container"></div>
';

echo "
<script>

Highcharts.chart('container', {
  chart: {
    type: 'line'
  },
  title: {
    text: 'CPU'
  },
  xAxis: {
    categories: [ ";
//echo $y;
echo "]
  },
  yAxis: {
    title: {
      text: 'CPU load %'
    }
  },
  plotOptions: {
    line: {
      dataLabels: {
        enabled: false
      },
      enableMouseTracking: false
    }
  },
  series: [{
    name: 'CPU load',
    data: [";
//7.0, 6.9, 9.5, 14.5, 18.4, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6
echo $x;

echo "]
  }

]
});
</script>
";
*/



echo '
<style>
table {
  width: 100%;
}

tr :first-child {
  width: 0;
  white-space: nowrap;
}

tr :last-child {
  width: 100%;
}

#container {
  min-width: 310px;
  max-width: 1400px;
  height: 300px;
  margin: 0 auto
}

</style>
';


echo '<hr>';
echo 'Показывает общую нагрузку на сервер:';

echo '<table width="100%" border=1>';
//echo "<tr>";

$lines = preg_split('/\\r\\n?|\\n/', $res2);

$total=count($lines);
for ($i = 0; $i < $total-1; $i++)
{

echo '<tr><td>';
echo '<div>';
//echo $lines[$i];
//echo trim(str_replace(' ','&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',$lines[$i]));
echo trim(str_replace(' ','&nbsp;',$lines[$i]));


if (strpos($lines[$i],'Cpu(s)')>0) {

$cpus=explode(':',explode(',',$lines[$i])[0])[1];

$cpus=preg_replace("/[^,.0-9]/", '', $cpus);

//echo "--------------:". str_replace(",", '',$cpus[0]);
//$pcpu=$pcpu.$cpus[1].';';
//echo 

//$max=SQLSelectOne('select max(updated) max from camshoter_config where parametr="cpu" ')['max'];
//if

//$sql='select * from camshoter_config where parametr="cpu" and updated>="'.date('Y-m-d H:i:s').'"';
$sql='select * from camshoter_config where parametr="cpu"';

$rec=SQLSelectOne($sql);
$rec['parametr']='cpu';
$rec['value']=str_replace(",", '',$cpus);
$rec['updated']=date('Y-m-d H:i:s');
if (!$rec['ID']) 
SQLInsert('camshoter_config', $rec);
else SQLUpdate('camshoter_config', $rec);

}

if (strpos($lines[$i],'average')>0) {

$cpus=explode(' ',$lines[$i]);

//echo "--------------:". str_replace(",", '',$cpus[13]);
//$pcpu=$pcpu.$cpus[1].';';
//echo 

//$max=SQLSelectOne('select max(updated) max from camshoter_config where parametr="average" ')['max'];
//if

//$sql='select * from camshoter_config where parametr="cpu" and updated>="'.date('Y-m-d H:i:s').'"';
$sql='select * from camshoter_config where parametr="average"';

$rec=SQLSelectOne($sql);
$rec['parametr']='average';
$rec['value']=str_replace(",", '',$cpus[13]);
$rec['updated']=date('Y-m-d H:i:s');
if (!$rec['ID']) 
SQLInsert('camshoter_config', $rec);
else SQLUpdate('camshoter_config', $rec);

}


//echo "<td  {text-align: justify;}>";
//echo '<p align="justify">';
echo '<span> </span></div> ';

echo "</td></tr>";
}
echo '</table><hr>';


//print_r( $output);
//echo "<br>";
echo 'Показывает список процессов ffmpeg:';

echo '<table width="100%" border=1>';
//echo "<tr>";

$lines = preg_split('/\\r\\n?|\\n/', $res);

$total=count($lines);
for ($i = 0; $i < $total-1; $i++)
{
//echo '<div>';


if ((strpos($lines[$i],'grep')>0)||(strpos($lines[$i],'sh')>0)
){
//echo "1233";
} else 

if ((strpos($lines[$i],'COMMAND')>0) ) 
{
echo '<tr align ="justify"><td align ="justify">';
//echo str_replace(' ','&nbsp;',$lines[$i]);
echo "%CPU&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TIME&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CMD";

}

else if  ((strpos($lines[$i],'ffmpeg')>0) ) 
{
echo '<tr><td bgcolor="red">';
//echo $lines[$i];
echo trim(str_replace(' ','&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',$lines[$i]));
} 
else  {
echo '<tr align ="justify"><td align ="justify">';
//echo $lines[$i];
//echo str_replace(' ','&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',$lines[$i]);
echo trim(str_replace(' ','&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',$lines[$i]));
}

//echo "<td  {text-align: justify;}>";
//echo '<p align="justify">';
echo '<span> </span></div> ';
echo '</div>';
echo "</td></tr>";
}

//echo ($res);
//echo "</tr>";
echo '</table>';
echo "<hr>";






        }



//        echo json_encode($result);
        exit;
    }


 if ($this->owner->action=='apps') {
  $this->redirect(ROOTHTML."module/".$this->name.".html");
 } else 
 $this->admin($out);
}







 function propertySetHandle($object, $property, $value) {
//debmes( 'propertySetHandle '.$object. '.'.$property.' '.$value, 'camshoter');
   $table='camshoter_devices';


$trigger=$object.'.'.$property;
//$sql="SELECT * FROM $table WHERE LINKED_OBJECT LIKE '".DBSafe($object)."' AND LINKED_PROPERTY LIKE '".DBSafe($property)."'";
$sql="SELECT * FROM $table WHERE (LINKED_OBJECT LIKE '".DBSafe($object)."' AND LINKED_PROPERTY LIKE '".DBSafe($property)."') OR (LINKED_OBJECT2 LIKE '".DBSafe($object)."' AND LINKED_PROPERTY2 LIKE '".DBSafe($property)."') OR (LINKED_OBJECT3 LIKE '".DBSafe($object)."' AND LINKED_PROPERTY3 LIKE '".DBSafe($property)."')";
//debmes($sql, 'camshoter');
   $properties=SQLSelect($sql);
//никого дома нет статус
$nobodyactive=gg('NobodyHomeMode.active');
//debmes( '$nobodyactive '.$nobodyactive, 'camshoter');

   $total=count($properties);
   if ($total) {
    for($i=0;$i<$total;$i++) {

$body=1;

if (($properties[$i]['SOMEBODYIGNORE']=='1')&& ($nobodyactive==0))
{$body=0;} 
else 
{$body=1;} 
//debmes( '----- '.$body, 'camshoter');
//debmes( '$body '.$body, 'camshoter');
//debmes( 'enable1 '.$properties[$i]['ENABLE1'], 'camshoter');

if ($properties[$i]['ENABLE1']=="1") {

	 if( ($properties[$i]['ID'])&&($properties[$i]['ENABLE']==1)&&($body==1)&&($value==1)) {
//$this->mainproccesss_test($properties,  $i);
//debmes( 'mainprocess enable1='.$properties[$i]['ENABLE1'], 'camshoter');
//debmes( 'mainprocess start', 'camshoter');
//$this->mainprocesss1($properties[$i],  $i);
$cmdd='
include_once(DIR_MODULES . "camshoter/camshoter.class.php");
$camshoter= new camshoter();
$cmd=array();

';
foreach ($properties[$i] as $val=>$name){
//$cmdd.='$cmd[]=array("'.$val.'"=>"'.$name.'");';
$cmdd.='$cmd["'.$val.'"]="'.$name.'";';
}
$cmdd.='
$camshoter->mainprocesss1($cmd,'.  $i.', "'.$trigger.'");
';
SetTimeOut('camshoter_timer '.$i,$cmdd, '0'); 
//debmes( 'mainprocess end', 'camshoter');



}
} else 
{
	 if( ($properties[$i]['ID'])&&($properties[$i]['ENABLE']==1)&&($body==1)) {
//$this->mainproccesss_test($properties,  $i);
//debmes( 'mainprocess enable1='.$properties[$i]['ENABLE1'], 'camshoter');
//debmes( 'mainprocess start', 'camshoter');
//$this->mainprocesss1($properties[$i],  $i);
$cmdd='
include_once(DIR_MODULES . "camshoter/camshoter.class.php");
$camshoter= new camshoter();
$cmd=array();

';
foreach ($properties[$i] as $val=>$name){
//$cmdd.='$cmd[]=array("'.$val.'"=>"'.$name.'");';
$cmdd.='$cmd["'.$val.'"]="'.$name.'";';
}
$cmdd.='
$camshoter->mainprocesss1($cmd,'.  $i.', "'.$trigger.'");
';
SetTimeOut('camshoter_timer '.$i,$cmdd, '0'); 



//debmes( 'mainprocess end', 'camshoter');



//$this->mainprocesss();
}
}
}
}

}
/////////////////////////////
/////////////////////////////
/////////////////////////////
/////////////////////////////


/**

*
* @access public
*/

/*
function mainprocesss($properties, $i){
//function mainproccesss (){

//debmes( 'run mainprocess '.$i, 'camshoter');
//debmes( $properties, 'camshoter');


//debmes('Сработал датчик движения на камере '.$properties[$i]['ID'],'camshoter');

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
//debmes('Видео сохранено  '.$savename,'camshoter');


//-vcodec copy -b 64k -acodec ac3

//exec('timeout -s INT 60s ffmpeg -y -i "'.$url.'"  -f image2  -updatefirst 1 '.$savenamethumb); 
//exec('timeout -s INT 60s ffmpeg -y -i "'.$savename.'"  -f image2  -updatefirst 1 '.$savenamethumb); 
//http://digilinux.ru/2010/10/21/how-to-split-frames-with-ffmpeg/

// первый кадр на обложку
exec('timeout -s INT 60s ffmpeg -y -i "'.$savename.'"  -r 1 -t 00:00:01 -f image2  -updatefirst 1 '.$savenamethumb); 

//раскадровка каждый 4 кадр в отдельную папку для определения наличия лиц
//exec('timeout -s INT 120s ffmpeg -y -i "'.$savename.'"  -r 0.25 -ss 00:00:00 -t 00:00:10 -f image2   '.$savenamethumbdir.'frames_%04d.png'); 


//нужна ли раскадровка, делаем ее в том случае, если есть токен майл ру

$cmd_rec = SQLSelectOne("SELECT * FROM camshoter_config where parametr='VISION_TOKEN'");
if ($cmd_rec['value']);
{
$savenamethumbdir=ROOT."cms/cached/nvr/cam".$properties[$i]['ID'].'/'.date('Y-m-d').'/'."cam".$properties[$i]['ID']."_".date('Y-m-d_His')."/";
 if (!file_exists($savenamethumbdir)) {
mkdir($savenamethumbdir, 0777, true);}
exec('timeout -s INT 120s ffmpeg -y -i "'.$savename.'"  -r 0.25  -f image2   '.$savenamethumbdir.'frames_%04d.jpg'); 
//debmes('Раскадровка сохранена  '.$savenamethumbdir,'camshoter');
}

}
else 
{
//windows
exec('C:\_majordomo\apps\ffmpeg\ffmpeg.exe -y -i "'.$url.'" -t '.$sec.' -f mp4 -vcodec libx264 -pix_fmt yuv420p -an -r 15 '.$savename); 
exec('C:\_majordomo\apps\ffmpeg\ffmpeg.exe -y -i "'.$savename.'"  -r 1 -t 00:00:01 -f image2  -updatefirst 1 '.$savenamethumb); 
}
copy($savenamethumb, $savenamelast);
}

//debmes(SQLSELECTONE("CHECK TABLE tlg_cmd"), 'camshoter');

///отправка в телеграм
if ((SQLSELECTONE("CHECK TABLE tlg_cmd")['Msg_text']=='OK')&&($properties[$i]['SENDTELEGRAM']==1)&&(SQLSELECTONE("CHECK TABLE tlg_user")['Msg_text']=='OK'))

 {	 
$fsize=filesize($savename);
$text='Зафиксировано изменение датчика '.$properties[$i]['LINKED_OBJECT'].' '.$properties[$i]['LINKED_OBJECT1'].' ' .$properties[$i]['LINKED_OBJECT2'].' на камере '.$properties[$i]['TITLE'] ;
include_once(DIR_MODULES . 'telegram/telegram.class.php');
$telegram_module = new telegram();


$ts=$properties[$i]['TELEGRAMUSERS'];

//debmes('telegram users  '.$ts,'camshoter');

$tsar=explode(',', $ts);

//debmes($tsar,'camshoter');


$total=count($tsar);
//debmes('total  '.$total,'camshoter');
for ($i = 0; $i < $total; $i++)
{



$user=SQLSElectOne("select * from tlg_user where ID='".$tsar[$i]."'")['USER_ID'];

//if ($iam=='img') {$telegram_module->sendImageToAll($savename,$text);}
//if (($iam=='video')&&($fsize>500)) {$telegram_module->sendVideoToAll($savename,$text);}


if ($iam=='img') {$telegram_module->sendImageToUser($user,$savename,$text);}
if (($iam=='video')&&($fsize>500)) {$telegram_module->sendVideoToUser($user,$savename,$text);}



//debmes('Файл '.$savename .' отправлен в телегу пользователю '.$user,'camshoter');


	 $properties[$i]['UPDATED']=date('Y-m-d H:i:s');
	 SQLUpdate('camshoter_devices', $properties[$i]);



//..if ($iam=='img') {$detect$this->mailvision_detect($savename);}
//if (($iam=='video')&&($fsize>500)) {$detect$this->mailvision_detect($savename);}




//определяем, есть ли на фото лицо

$this->detectface($properties, $i, $savenamethumbdir);
}
}
}

*/

function mainprocesss1($properties, $i, $trigger){
//function mainproccesss (){

//debmes( 'run mainprocess '.$i, 'camshoter');
//debmes( $properties, 'camshoter');


//debmes('Сработал датчик движения на камере '.$properties[$i]['ID'],'camshoter');

$savepath=ROOT."cms/cached/nvr/cam".$properties['ID'].'/'.date('Y-m-d').'/';
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



if ($properties['TYPE']=='snapshot')
{
$iam='img';
$image_url=$properties['URL'];
$savename=$savepath."cam".$properties['ID']."_".date('Y-m-d_His').".jpg"; // куда сохранять
$savenamelast=$savelast."cam".$properties['ID'].".jpg"; // куда сохранять
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

if ($properties['TYPE']=='snapshot_diff')
{
$iam='img';
$image_url=$properties['URL'];
$savename=$savepath."cam".$properties['ID']."_".date('Y-m-d_His').".jpg"; // куда сохранять
//$savename=$savepath."cam".$properties['ID']."_".date('Y-m-d').".jpg"; // куда сохранять
$savenameid=$savepath."cam".$properties['ID']."_".date('Y-m-d').".id"; // куда сохранять
$savenamelast=$savelast."cam".$properties['ID'].".jpg"; // куда сохранять
$savenamelast1=$savelast."cam".$properties['ID'].".jpg1"; // куда сохранять
$savenamethumb=$savename;

$result=getURL($image_url,0);

if ($result) {
//SaveFile($savename, $result);
SaveFile($savenamelast, $result);

}else {
$result=file_get_contents($url); //скачиваем картинку с камеры 
//file_put_contents($savename, $result);
file_put_contents($savenamelast, $result);
}

//$imageid=$this->getimageid2($savenamelast);
//debmes( '$imageid:   '.$imageid, 'camshoter');

/*
$oldimageid=SQLSelectOne("select * from  camshoter_config where parametr='imageid".$properties['ID']."'")['value'];

debmes( '$oldimageid:'.$oldimageid, 'camshoter');

$cmd=SQLSelectOne("select * from  camshoter_config where parametr='imageid".$properties['ID']."'");

if (!$cmd['ID'])
{
$cmd['value']= $imageid;
$cmd['updated']=date('Y-m-d H:i:s');
$cmd['parametr']= 'imageid'.$properties['ID'];
sqlinsert('camshoter_config',$cmd);}
else 
{
$cmd['value']= $imageid;
$cmd['updated']=date('Y-m-d H:i:s');
$cmd['parametr']= 'imageid'.$properties['ID'];
sqlupdate('camshoter_config',$cmd);
}


//if  ($oldimageid<> $imageid)
$ver=$this->imagediff2($imageid,$oldimageid);
debmes('ver:'.$ver, 'camshoter');

*/

$ver=$this->imagecompare2($savenamelast1,$savenamelast);
debmes('ver:'.$ver, 'camshoter');


$sensity=$properties['SENSITY'];
if (!$sensity) $sensity=60;

if ($ver<$sensity)  
{
if ($result) {
SaveFile($savename, $result);
}else {
$result=file_get_contents($url); //скачиваем картинку с камеры 
file_put_contents($savename, $result);



}

if (($properties['LINKED_OBJECT4'])&&($properties['LINKED_PROPERTY4'])) setglobal($properties['LINKED_OBJECT4'].'.'.$properties['LINKED_PROPERTY4'],1);
$logrec=SQLSelectOne('select * from camshoter_log where ID="dummy"');
if (!$logrec['ID'])
{
$cnt=substr_count( $savename, '/');
$upfolder=explode('/',$savename);

$nado='';

for($i=0; $iii<=$cnt; $iii++){

if ($upfolder[$iii]=='cms') $nado='1';
if ($nado=='1') $localpath.=$upfolder[$iii].'/';
}
$localpath=rtrim($localpath,'/');


//$trigger=$properties['LINKED_OBJECT'].' '.$properties['LINKED_OBJECT1'].' ' .$properties['LINKED_OBJECT2'];

//$localpath='';
$logrec['type']=$properties['TYPE'];
$logrec['camid']=$properties['ID'];
$logrec['path']=$savename;
$logrec['pathroot']=$localpath;
$logrec['message']=$text;
$logrec['trigger']=$trigger;
$logrec['updated']=date('Y-m-d H:i:s');
SQLInsert('camshoter_log', $logrec);
}


}




SaveFile($savenamelast1, $result);
//SaveFile($savenameid, $imageid);


}


if (($properties['TYPE']=='rtsp')&&($properties['METHOD']=='mp4'))
{
$iam='video';
$url=$properties['URL'];
$sec=$properties['SEC'];
$savename=$savepath."cam".$properties['ID']."_".date('Y-m-d_His').".mp4"; // куда сохранять
$savenamethumb=$savepath."cam".$properties['ID']."_".date('Y-m-d_His').".jpg"; // куда сохранять
$savenamethumbdir=$savepath."cam".$properties['ID']."_".date('Y-m-d_His'); // куда сохранять
$savenamelast=$savelast."cam".$properties['ID'].".jpg"; // куда сохранять
$savenameface=$savefacesdir."cam".$properties['ID']."_".date('Y-m-d_His').".jpg"; // куда сохранять

//linux
if (substr(php_uname(),0,5)=='Linux')  {
//exec('timeout -s INT 60s ffmpeg -y -i "'.$url.'" -t '.$sec.' -f mp4 -vcodec libx264 -pix_fmt yuv420p -an -r 15 '.$savename); 
//  exec('timeout -s INT 60s ffmpeg -y -i "'.$url.'" -t '.$sec.' -f mp4 -vcodec copy -pix_fmt yuv420p -acodec ac3 -an -r 15 '.$savename); 
//  exec('timeout -s INT 60s ffmpeg -y -i "'.$url.'" -t '.$sec.' -f mp4 -vcodec copy -pix_fmt yuv420p -acodec pcm_s16le -an -r 15 '.$savename); 
if  ($properties['FFMPEGCMD']=="")
{
//  exec('timeout -s INT 60s ffmpeg -y -i "'.$url.'" -t '.$sec.' -f mp4 -vcodec copy -pix_fmt yuv420p -acodec copy -an -r 15 '.$savename); 
//  exec('ffmpeg -y -i "'.$url.'" -t '.$sec.' -f mp4 -vcodec copy -pix_fmt yuv420p -acodec copy -an -r 15 '.' 2>&1'.$savename); 
$cmd='ffmpeg -y -i "'.$url.'" -t '.$sec.' -f mp4 -vcodec copy -pix_fmt yuv420p -acodec copy -an -r 15 '.$savename;
$res = exec($cmd . ' 2>&1', $output);

 } else
{
$cmd='timeout -s INT 60s '.str_replace('#savename',$savename, str_replace('#sec',$sec, $properties['FFMPEGCMD']));
exec($cmd); 
}
//debmes('Видео сохранено  '.$savename,'camshoter');


//-vcodec copy -b 64k -acodec ac3

//exec('timeout -s INT 60s ffmpeg -y -i "'.$url.'"  -f image2  -updatefirst 1 '.$savenamethumb); 
//exec('timeout -s INT 60s ffmpeg -y -i "'.$savename.'"  -f image2  -updatefirst 1 '.$savenamethumb); 
//http://digilinux.ru/2010/10/21/how-to-split-frames-with-ffmpeg/

// первый кадр на обложку
exec('timeout -s INT 60s ffmpeg -y -i "'.$savename.'"  -r 1 -t 00:00:01 -f image2  -updatefirst 1 '.$savenamethumb); 

//раскадровка каждый 4 кадр в отдельную папку для определения наличия лиц
//exec('timeout -s INT 120s ffmpeg -y -i "'.$savename.'"  -r 0.25 -ss 00:00:00 -t 00:00:10 -f image2   '.$savenamethumbdir.'frames_%04d.png'); 


//нужна ли раскадровка, делаем ее в том случае, если есть токен майл ру

$cmd_rec = SQLSelectOne("SELECT * FROM camshoter_config where parametr='VISION_TOKEN'");
if ($cmd_rec['value']);
{
$savenamethumbdir=ROOT."cms/cached/nvr/cam".$properties['ID'].'/'.date('Y-m-d').'/'."cam".$properties['ID']."_".date('Y-m-d_His')."/";
 if (!file_exists($savenamethumbdir)) {
mkdir($savenamethumbdir, 0777, true);}
exec('timeout -s INT 120s ffmpeg -y -i "'.$savename.'"  -r 0.25  -f image2   '.$savenamethumbdir.'frames_%04d.jpg'); 
//debmes('Раскадровка сохранена  '.$savenamethumbdir,'camshoter');
}

}
else 
{
//windows
exec('C:\_majordomo\apps\ffmpeg\ffmpeg.exe -y -i "'.$url.'" -t '.$sec.' -f mp4 -vcodec libx264 -pix_fmt yuv420p -an -r 15 '.$savename); 
exec('C:\_majordomo\apps\ffmpeg\ffmpeg.exe -y -i "'.$savename.'"  -r 1 -t 00:00:01 -f image2  -updatefirst 1 '.$savenamethumb); 
}
if (file_exists($savenamethumb)) copy($savenamethumb, $savenamelast);
}

//debmes(SQLSELECTONE("CHECK TABLE tlg_cmd"), 'camshoter');

///отправка в телеграм
if ((SQLSELECTONE("CHECK TABLE tlg_cmd")['Msg_text']=='OK')&&($properties['SENDTELEGRAM']==1)&&(SQLSELECTONE("CHECK TABLE tlg_user")['Msg_text']=='OK'))

 {	 
$fsize=filesize($savename);

//$text='Зафиксировано изменение датчика '.$properties['LINKED_OBJECT'].' '.$properties['LINKED_OBJECT1'].' ' .$properties['LINKED_OBJECT2'].' на камере '.$properties['TITLE'] ;
$text='Зафиксировано изменение датчика '.$trigger.' на камере '.$properties['TITLE'] ;
include_once(DIR_MODULES . 'telegram/telegram.class.php');
$telegram_module = new telegram();


$ts=$properties['TELEGRAMUSERS'];

//debmes('telegram users  '.$ts,'camshoter');

$tsar=explode(',', $ts);

//debmes($tsar,'camshoter');


$total=count($tsar);
//debmes('total  '.$total,'camshoter');
for ($ii = 0; $ii < $total; $ii++)
{



$user=SQLSElectOne("select * from tlg_user where ID='".$tsar[$ii]."'")['USER_ID'];

//if ($iam=='img') {$telegram_module->sendImageToAll($savename,$text);}
//if (($iam=='video')&&($fsize>500)) {$telegram_module->sendVideoToAll($savename,$text);}


if ($iam=='img') {$telegram_module->sendImageToUser($user,$savename,$text);}
if (($iam=='video')&&($fsize>500)) {$telegram_module->sendVideoToUser($user,$savename,$text);}



//debmes('Файл '.$savename .' отправлен в телегу пользователю '.$user,'camshoter');


	 $properties['UPDATED']=date('Y-m-d H:i:s');
	 SQLUpdate('camshoter_devices', $properties);



//..if ($iam=='img') {$detect$this->mailvision_detect($savename);}
//if (($iam=='video')&&($fsize>500)) {$detect$this->mailvision_detect($savename);}




//определяем, есть ли на фото лицо

//$this->detectface($properties, $i, $savenamethumbdir);
}
}

$logrec=SQLSelectOne('select * from camshoter_log where ID="dummy"');
if (!$logrec['ID'])
{
$cnt=substr_count( $savename, '/');
$upfolder=explode('/',$savename);

$nado='';

for($i=0; $iii<=$cnt; $iii++){

if ($upfolder[$iii]=='cms') $nado='1';
if ($nado=='1') $localpath.=$upfolder[$iii].'/';
}
$localpath=rtrim($localpath,'/');


//$trigger=$properties['LINKED_OBJECT'].' '.$properties['LINKED_OBJECT1'].' ' .$properties['LINKED_OBJECT2'];

//$localpath='';
$logrec['type']=$properties['TYPE'];
$logrec['camid']=$properties['ID'];
$logrec['path']=$savename;
$logrec['pathroot']=$localpath;
$logrec['message']=$text;
$logrec['trigger']=$trigger;
$logrec['updated']=date('Y-m-d H:i:s');
if ($trigger<>'snapshot_diff') SQLInsert('camshoter_log', $logrec);
}



}

   




function detectface ($properties, $i, $savenamethumbdir){
//debmes( 'run mainprocesstest '.$i, 'camshoter');
//debmes( $properties, 'camshoter');
$fddpath=DIR_MODULES.$this->name."/FaceDetector.php";
//debmes('Запускаем процесс facedetect '.$fddpath,'camshoter');
//error_reporting(0);
//include "FaceDetector.php";

//require(DIR_MODULES.$this->name.'/FaceDetector.php');
include $fddpath;

$face_detect = new Face_Detector('detection.dat');
//debmes('facedetect открыт','camshoter');




///идем по кадрам, сохраненным из видео
//debmes('Идем по кадрам '.$savenamethumbdir,'camshoter');

if (($savenamethumbdir)&&($savenamethumbdir<>"")&& (is_dir($savenamethumbdir))){
 foreach (scandir($savenamethumbdir) as $v) 
{
//debmes('Проверяем фото '.$savenamethumbdir.$v.' на наличие лиц','camshoter');
if ($face_detect->face_detect($savenamethumbdir.$v)) 
{$face=1;
//debmes('В кадре лицо  '.$savenamethumbdir.$v,'camshoter');
$face_detect->cropsave($savenameface);
//$face_detect->cropFace2('new.jpg');
} else $face=0; 

//if ($face==1) $this->mailvision_detect_face($savenameface, $id);
}}

$this->mailvision_detect($savenamethumb, $id);



}



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

//$this->getsizeall();
//$this->manageallfolders();
//$this->hourly();


$cmdd='
include_once(DIR_MODULES . "camshoter/camshoter.class.php");
$camshoter= new camshoter();
$camshoter->getsizeall();';
SetTimeOut('getsizeall '.$i,$cmdd, '60'); 



$cmdd='
include_once(DIR_MODULES . "camshoter/camshoter.class.php");
$camshoter= new camshoter();
$camshoter->hourly();';
SetTimeOut('hourly '.$i,$cmdd, ''); 


$cmdd='
include_once(DIR_MODULES . "camshoter/camshoter.class.php");
$camshoter= new camshoter();
$camshoter->manageallfolders();';
SetTimeOut('manageallfolders '.$i,$cmdd, '120'); 


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
 camshoter_devices: ENABLE1 int(1) 
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

 camshoter_devices: LINKED_OBJECT2 varchar(255) NOT NULL DEFAULT ''
 camshoter_devices: LINKED_PROPERTY2 varchar(255) NOT NULL DEFAULT ''

 camshoter_devices: LINKED_OBJECT3 varchar(255) NOT NULL DEFAULT ''
 camshoter_devices: LINKED_PROPERTY3 varchar(255) NOT NULL DEFAULT ''

 camshoter_devices: LINKED_OBJECT4 varchar(255) NOT NULL DEFAULT ''
 camshoter_devices: LINKED_PROPERTY4 varchar(255) NOT NULL DEFAULT ''

 camshoter_devices: SENSITY varchar(255) NOT NULL DEFAULT ''
 camshoter_devices: HOURLY varchar(255) NOT NULL DEFAULT ''



 camshoter_log: ID int(10) unsigned NOT NULL auto_increment
 camshoter_log: type varchar(100)
 camshoter_log: camid varchar(10)
 camshoter_log: path varchar(100)
 camshoter_log: pathroot varchar(100)
 camshoter_log: trigger varchar(100)
 camshoter_log: responce varchar(1000)
 camshoter_log: peoples varchar(100)
 camshoter_log: faces varchar(100)
 camshoter_log: message varchar(10000)  
 camshoter_log: updated datetime


EOD;
  parent::dbInstall($data);

  $data = <<<EOD
 camshoter_config: ID int(10) unsigned NOT NULL auto_increment
 camshoter_config: parametr varchar(300)
 camshoter_config: value varchar(10000)  
 camshoter_config: updated datetime
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



//debmes('camshoter storage dir:'.$dir, 'camshoter');
//debmes('camshoter storage cnt:'.$cnt, 'camshoter');
//debmes('camshoter upfoler:'.$upfoler, 'camshoter');
//debmes('camshoter upfoler1:'.$upfoler1, 'camshoter');

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

//debmes('вызван getusers '.$dir, 'camshoter');


  $tempusers=SQLSelect("SELECT ID, NAME FROM users ORDER BY NAME");
  $users_total=count($tempusers);
  for($users_i=0;$users_i<$users_total;$users_i++) {
//   $user_id_opt[$tmp[$users_i]['ID']]=$tmp[$users_i]['NAME'];
//$users[]=$tempusers['NAME'];
  }

$users=$tempusers;
//debmes($users, 'camshoter');

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


//debmes('$upfoler '.$upfoler, 'camshoter');
//debmes('$upfoler1 '.$upfoler1, 'camshoter');


 $files = array();



if (($dir)&&($dir<>"")&&is_dir($dir)){
 foreach (scandir($dir) as $v) 


{
//debmes($v, 'camshoter');
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
//$files[] =array("FILE"=>$upfoler1."/".$v,'SIZETHMB'=>$sizethmb, 'ID'=>$userid, 'USERS'=>$users, 'USERNAME'=>$username, 'META'=>$meta);
$files[] =array("CAMID"=>preg_replace("/[^0-9]/", '',$v),"FILE"=>$upfoler."/".$v,'SIZETHMB'=>$sizethmb, 'ID'=>$userid, 'USERS'=>$users, 'USERNAME'=>$username, 'META'=>$meta);
//$files[] =array("FILE"=>$dir."/".$v,'SIZETHMB'=>$sizethmb, 'ID'=>$userid, 'USERS'=>$users, 'USERNAME'=>$username, 'META'=>$meta);
//debmes($files, 'camshoter');

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
if (is_dir($f)) {  $dh=opendir($f); 
                while(($file=readdir($dh))!==false) 
                { 
                        if($file=='.' || $file=='..') continue; 
                        if(is_file($f.'/'.$file)) $size+=filesize($f.'/'.$file); 
                        else $size+=$this->show_size($f.'/'.$file,false); 
                } 
                closedir($dh); }
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
if (!is_array($files))   return 0; else return count($files);  
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



//Генерация ключа-изображения

//https://habr.com/ru/post/55926/

function getimageid2($image)
{
  //Размеры исходного изображения
  $size=getimagesize($image);

  //Исходное изображение
  $image=imagecreatefrompng($image);

  //Маска
  $zone=imagecreate(20,20);

  //Копируем изображение в маску
  imagecopyresized($zone,$image,0,0,0,0,20,20,$size[0],$size[1]);

  //Будущая маска
  $colormap=array();

  //Базовый цвет изображения
  $average=0;

  //Результат
  $result=array();

   //Заполняем маску и вычисляем базовый цвет
  for($x=0;$x<20;$x++)
    for($y=0;$y<20;$y++)
    {
      $color=imagecolorat($zone,$x,$y);
      $color=imagecolorsforindex($zone,$color);

      //Вычисление яркости было подсказано хабраюзером Ryotsuke
      $colormap[$x][$y]= 0.212671 * $color['red'] + 0.715160 * $color['green'] + 0.072169 * $color['blue'];

      $average += $colormap[$x][$y];
    }

  //Базовый цвет
  $average /= 400;

  //Генерируем ключ строку
  for($x=0;$x<20;$x++)
    for($y=0;$y<20;$y++)
          $result[]=($x<10?$x:chr($x+97)).($y<10?$y:chr($y+97)).round(2*$colormap[$x][$y]/$average);

  //Возвращаем ключ
  return join(' ',$result);
}

// This source code was highlighted with Source Code Highlighter.


///Вычисление "похожести" двух изображений
//http://www.manhunter.ru/webmaster/1284_sravnenie_izobrazheniy_na_php.html
function imagediff2($image,$desc)
{
  $image=explode(' ',$image);
  $desc=explode(' ',$desc);

  $result=0;

  foreach($image as $bit)
    if(in_array($bit,$desc))
      $result++;

   return $result/((count($image)+count($desc))/2);
}

function imagecompare2($file1,$file2)
{ 
$im1=ImageCreateFromJPEG($file1);
$im2=ImageCreateFromJPEG($file2);
// Размеры изображения
$width=ImageSX($im1);
$height=ImageSY($im1);
$count=0;
for ($x=0; $x<$width; $x++) {
    for ($y=0; $y<$height; $y++) {
        // Цвет точки первого изображения
        $rgb1=ImageColorAt($im1,$x,$y);
        $R1=($rgb1 >> 16) & 0xFF;
        $G1=($rgb1 >> 8) & 0xFF;
        $B1=$rgb1 & 0xFF;
 
        // Цвет точки второго изображения
        $rgb2=ImageColorAt($im2,$x,$y);
        $R2=($rgb2 >> 16) & 0xFF;
        $G2=($rgb2 >> 8) & 0xFF;
        $B2=$rgb2 & 0xFF;
 
        // Перевести в оттенки серого
        $gray1=(0.2126*$R1+0.7152*$G1+0.0722*$B1);
        $gray2=(0.2126*$R2+0.7152*$G2+0.0722*$B2);
 
        // Если получился цвет, близкий к черному,
        // то считаем, что цвета точек условно равны
        if (($gray1^$gray2) < 5) {
            $count++;
        }
    }
}
 
// Прибраться за собой
ImageDestroy($im1);
ImageDestroy($im2);
 
// Коэффициент похожести изображений в процентах
$similarity=intval($count/$x/$y*100);
return $similarity;
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
