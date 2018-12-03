<?php
/*
* @version 0.1 (wizard)
*/

  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }



//  if ($this->mode=='confirm') 
/*
{
//  $rec=SQLSelectOne("SELECT * FROM $table_name WHERE ID='$id'");
   $ok=1;
 
   global $arcdate;
if ($arcdate="" ) { $rec['ARCDATE']=date('Ymd');} else 
{   $rec['ARCDATE']=str_replace("-","",$arcdate);}


}
*/
///////////////////////

  $table_name='camshoter_devices';
//  $rec=SQLSelectOne("SELECT * FROM $table_name WHERE ID='$id' and IPADDR='$ipaddr'");


  $rec=SQLSelectOne("SELECT * FROM $table_name WHERE ID='$id'");
  if ($this->mode=='update') {
//  $rec=SQLSelectOne("SELECT * FROM $table_name WHERE ID='$id'");
   $ok=1;
 
   global $title;
   $rec['TITLE']=$title;

   global $type;
   $rec['TYPE']=$type;

   global $url;
   $rec['URL']=$url;

   global $srok;
   $rec['SROK']=$srok;

  global $ipaddr;
   $rec['IPADDR']=$ipaddr;



   global $method;
   $rec['METHOD']=$method;


  global $linked_object;
  global $linked_property;
  $rec['LINKED_OBJECT']=trim($linked_object);
  $rec['LINKED_PROPERTY']=trim($linked_property);
  addLinkedProperty($rec['LINKED_OBJECT'], $rec['LINKED_PROPERTY'], $this->name);   
   //UPDATING RECORD
//sg('test.merk',$ok);
   if ($ok) {
    if ($rec['ID']) {
     SQLUpdate($table_name, $rec); // update
    } else {
     $new_rec=1;
     $rec['ID']=SQLInsert($table_name, $rec); // adding new record
    }
    $out['OK']=1;


   } else {
    $out['ERR']=1;
   }
  }
   

    else {
    $out['ERR']=1;
   }
  



  if (is_array($rec)) {
   foreach($rec as $k=>$v) {
    if (!is_array($v)) {
     $rec[$k]=htmlspecialchars($v);
    }
   }
  }
  outHash($rec, $out);

