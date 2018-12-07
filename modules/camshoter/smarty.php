<?php

if ($argc < 4 )
{
    exit( "Usage: $argv[0] <url> <path> <meta>\n" );
}

$url = $argv[1];
$path = $argv[2];
$meta = $argv[3];

$filenames = array($path);
$files = array();
foreach ($filenames as $f)
{
    if (strlen($f) == 0)
    {
        # just to make correct multipart/form-data request
        $files[$f] = 'fake content';
    }
    else
    {
        $files[$f] = file_get_contents($f);
    }
}

$boundary = uniqid();
$delimiter = '-------------' . $boundary;

$fields = array("meta"=> $meta);
$post_data = build_data_files($boundary, $fields, $files);

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  #CURLOPT_VERBOSE => true,
  CURLOPT_RETURNTRANSFER => 1,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_POST => 1,
  CURLOPT_POSTFIELDS => $post_data,
  CURLOPT_HTTPHEADER => array(
    "Content-Type: multipart/form-data; boundary=" . $delimiter,
    "Content-Length: " . strlen($post_data),
  ),
));

$response = curl_exec($curl);

$info = curl_getinfo($curl);
echo "code: ${info['http_code']}\n";
var_dump($response);
curl_close($curl);

function build_data_files($boundary, $fields, $files)
{
    $data = '';
    $eol = "\r\n";

    $delimiter = '-------------' . $boundary;

    foreach ($fields as $name => $content)
    {
        $data .= "--" . $delimiter . $eol
            . 'Content-Disposition: form-data; name="' . $name . "\"".$eol.$eol
            . $content . $eol;
    }

    foreach ($files as $name => $content)
    {
        $data .= "--" . $delimiter . $eol
            . 'Content-Disposition: form-data; name="' . $name . '"; filename="' . $name . '"' . $eol
            . 'Content-Type: image/jpeg'.$eol
            . 'Content-Transfer-Encoding: binary'.$eol
            ;

        $data .= $eol;
        $data .= $content . $eol;
    }
    $data .= "--" . $delimiter . "--".$eol;
    return $data;
}

?>
