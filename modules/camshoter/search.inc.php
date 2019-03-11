<?php
/*
* @version 0.1 (wizard)
*/
 global $session;
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $qry="1";
  // search filters
  //searching 'TITLE' (varchar)
  global $title;
  if ($title!='') {
   $qry.=" AND TITLE LIKE '%".DBSafe($title)."%'";
   $out['TITLE']=$title;
  }
  // QUERY READY
  global $save_qry;
  if ($save_qry) {
   $qry=$session->data['espcounter_devices_qry'];
  } else {
   $session->data['espcounter_devices_qry']=$qry;
  }
  if (!$qry) $qry="1";
  // FIELDS ORDER
  global $sortby_snmpdevices;
  if (!$sortby_snmpdevices) {
   $sortby_snmpdevices=$session->data['espcounter_devices_sort'];
  } else {
   if ($session->data['espcounter_devices_sort']==$sortby_snmpdevices) {
    if (Is_Integer(strpos($sortby_snmpdevices, ' DESC'))) {
     $sortby_snmpdevices=str_replace(' DESC', '', $sortby_snmpdevices);
    } else {
     $sortby_snmpdevices=$sortby_snmpdevices." DESC";
    }
   }
   $session->data['espcounter_devices_sort']=$sortby_snmpdevices;
  }
  if (!$sortby_snmpdevices) $sortby_snmpdevices="TITLE";
  $out['SORTBY']=$sortby_snmpdevices;
  // SEARCH RESULTS
  $res=SQLSelect("SELECT camshoter_devices.*, DATE_FORMAT(FROM_UNIXTIME(TS), '%d-%m-%Y %H:%m') as TS3 FROM camshoter_devices WHERE $qry ORDER BY ".$sortby_snmpdevices);
  $days=$res[0]['SROK'];
//  $iddev=$res[0]['ID'];

  if ($res[0]['ID']) {
   colorizeArray($res);
   $total=count($res);
debmes('total:'.$total, 'camshoter');
   for($i=0;$i<$total;$i++) {

    // some action for every record if required
   }






   $out['RESULT']=$res;

//debmes($res, 'camshoter');



/*

//$res2 = SQLSelect("select ID,NAME,ADMIN, (SELECT count(*) FROM tlg_user_cmd where CMD_ID='$id' and tlg_user_cmd.USER_ID=tlg_user.ID) as ACCESS_USER from tlg_user");
$sql="select ID,NAME,ADMIN ,(SELECT  count(*)   FROM camshoter_devices   where ID='".$res[0]['ID']."' and  TELEGRAMUSERS=tlg_user.ID )  as ACCESS_USER  from tlg_user ";
debmes($sql, 'camshoter');

$res2 = SQLSelect($sql);

debmes($res2, 'camshoter');

if ($res[0]) {
    $out['LIST_ACCESS'] = $res2;
}


$res = SQLSelect("SELECT * from camshoter_devices where ID='$id'");
if ($res[0]) {
    $qs    = array();
    foreach ($res as $row) {
        $qs[] = $row['TELEGRAMUSERS'];
    }
    $out['USER_ID'] = implode(',', $qs);
   debmes(implode(',', $qs), 'camshoter');
}

*/

//////////////////////////
//////////////////////////

  if ($this->tab=='devcount') {





$iddev=$this->id;
$date1=date('Y-m-d');
$date2=strtotime("-$days day");



$arcar=$this->createDateRangeArray(date('Y-m-d',$date2), $date1);
//print_r($arcar);
$out['ARCDATES']=$arcar;





/*
global $arcdate;
if ($arcdate="" ) 
{ $out['ARCDATE']=date('Ymd');} else 
{ $out['ARCDATE']=str_replace("-","",$arcdate);}
*/


///////////////////
//массив дат для комбобокса
$arcar=$this->createDateRangeArray(date('Y-m-d',$date2), $date1);
//print_r($arcar);
//$out['FILES']=$arcar;



$savepath=ROOT."cms/cached/nvr/cam".$iddev;


//$folderdata=date('Ymd');
//$folderdata=str_replace("-","",$arcdate);
//$folderdata='20181203';

//echo $savepath."<br>";

////////////////////
////////////////////
////////////////////
///список файлов с датой из фильтра
global $arcdate;

if (!$arcdate)  {$folderdata=date('Y-m-d');
//echo "yes";
} else 
{
//$folderdata=str_replace("-","",$arcdate);
$folderdata=$arcdate;
//echo "no";
}
$out['ARCDATE']=$folderdata;	
$gfolder=$savepath.'/'.$folderdata."/";
//echo $gfolder."<br>";

$files=$this->getfiles($gfolder);
//print_r($files);
//jpeg
$out['FILES']=$files;




// $this->redirect("?view_mode=indata_edit&tab=devcount&id=".$this->id);


}




  } 



?>

