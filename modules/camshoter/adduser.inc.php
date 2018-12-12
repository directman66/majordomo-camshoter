<?php
/*
* @version 0.1 (wizard)
*/

  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }



// В PHP 4.1.0 и более ранних версиях следует использовать $HTTP_POST_FILES
// вместо $_FILES.


$users=ROOT."cms/cached/nvr/users/";
if (!file_exists($users)) {
mkdir($users, 0777, true);}


$uploaddir = $users;
$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);

//echo '<pre>';
if (!move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
//    echo "Файл корректен и был успешно загружен.\n";
//} else {
    echo "Возможная атака с помощью файловой загрузки!\n";
}

//echo 'Некоторая отладочная информация:';
//print_r($_FILES);

//print "</pre>";

