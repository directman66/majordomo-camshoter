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
  $iddev=$res[0]['ID'];

  if ($res[0]['ID']) {
   colorizeArray($res);
   $total=count($res);
   for($i=0;$i<$total;$i++) {

    // some action for every record if required
   }
   $out['RESULT']=$res;

//////////////////////////
//////////////////////////

  if ($this->tab=='devcount') {


$date1=date('Y-m-d');
$date2=strtotime("-$days day");



$arcar=$this->createDateRangeArray(date('Y-m-d',$date2), $date1);
//print_r($arcar);
$out['ARCDATES']=$arcar;
///////////////////


$arcar=$this->createDateRangeArray(date('Y-m-d',$date2), $date1);
//print_r($arcar);
$out['FILES']=$arcar;



$savepath=ROOT."cms/cached/nvr/cam".$iddev;
$folderdata=date('Ymd');
//$folderdata='20181203';

//echo $savepath."<br>";

$files=$this->getfiles($savepath,$folderdata);
//print_r($files);
$out['FILES']=$files;

}



  } 




?>
