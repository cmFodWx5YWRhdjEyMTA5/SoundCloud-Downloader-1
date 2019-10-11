<?php
ini_set('max_execution_time', 600);
if (isset($_POST['add']) && !empty(trim($_POST['add']))) {
	function custom_curl($url, $method = "get", $data = [], $header = [])
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 2);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_CAINFO, "cacert.pem");
    if($method == 'post') curl_setopt($ch, CURLOPT_POST, 1);
    if($data != []) curl_setopt($ch, CURLOPT_POSTFIELDS, 
              http_build_query($data).'\n');

    if ($header != []) curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.167 Safari/537.36');

    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    $html = curl_exec($ch);
    curl_close($ch);
    return $html;
}

function clean($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}

$data = custom_curl($_POST['add']);
preg_match_all('/<script crossorigin src="(.*?)">.*?<\/script>/uis', $data, $script);
/*preg_match('/<section class="tracklist">(.*?)<\/section>/uis', $data, $section);*/

//if (!isset($title[1])) header('location: ');
/*preg_match_all('/<a.*?itemprop="url".*?href="(.*?)".*?>.*?<\/a>/uis', $section[1], $tracks);
preg_match_all('/{\"id\":([0-9]+),\"kind\":\"track\"}/uis', $data, $numtracks);*/
//$ct = str_replace(['/', '\\', '?', '*', ':', '<','>','|','!'], '', trim($title[1]));
//$ct = str_replace(' ', '_', $ct);
//$na = array_reverse($sizes[1]);
//var_dump($section);
//var_dump($tracks[1]);

$rsc = array_reverse($script[1]);
$tok = "";
    foreach ($rsc as $key => $script) {
        $scr = custom_curl($script);
        preg_match("/client_id:\"(.*?)\"/sui", $scr, $res);
        if(isset($res[1])) {
            $tok = $res[1];
            break;
        }
    }

$all_tracks = custom_curl('https://api.soundcloud.com/resolve.json?url='.$_POST['add'].'&client_id='.$tok);
$thee_tracks = json_decode($all_tracks, true);
$named = array_reverse(explode('/', $_POST['add']))[0];
$cleaned_named = clean($named);
if(!is_dir('dl/'.$cleaned_named)) mkdir('dl/'.$cleaned_named);

$pathes = [];

if (isset($thee_tracks['tracks'])) {
    
    foreach($thee_tracks['tracks'] as $trrrk)
{
    $track_name = clean($trrrk['title']);
    if (!is_file('dl/'.$cleaned_named.'/'.$track_name.'.mp3')) {
    $datum = custom_curl($trrrk['stream_url'].'?client_id='.$tok);
    
    file_put_contents('dl/'.$cleaned_named.'/'.$track_name.'.mp3', $datum);
    }
    $pathes[] = 'dl/'.$cleaned_named.'/'.$track_name.'.mp3'; 
}

$zip = new ZipArchive;
$zip->open('zip/'.$cleaned_named.'.zip', ZipArchive::CREATE);
foreach(glob('dl/'.$cleaned_named.'/*') as $file) {
    $zip->addFile($file);
}
$zip->close();

foreach($pathes as $path){
    unlink($path);
}
rmdir('dl/'.$cleaned_named.'/');


$_msg = '<a style="    background: #ddbbf1;
display: inline-block;
border-radius: 40px;font-size:20px; font-family:calibri; padding: 10px 15px;" href="zip/'.$cleaned_named.'.zip'.'">
<span style="    font-size: 11px;
    padding-right: 20px;
    font-weight: bold;
    color: #fff;
    vertical-align: middle;">DOWNLOAD</span>'.$cleaned_named.'.zip'.'</a>';
    
} else {
     $track_name = clean($thee_tracks['title']);
     
     if (!is_file('zip/'.$track_name.'.mp3')) {
         $datum = custom_curl($thee_tracks['stream_url'].'?client_id='.$tok);
    
    file_put_contents('zip/'.$track_name.'.mp3', $datum);
     }
    
    
    $_msg = '<a style="    background: #ddbbf1;
    display: inline-block;
    border-radius: 40px;font-size:20px; font-family:calibri; padding: 10px 15px;" href="zip/'.$track_name.'.mp3'.'">
    <span style="    font-size: 11px;
    padding-right: 20px;
    font-weight: bold;
    color: #fff;
    vertical-align: middle;">DOWNLOAD</span>'.$track_name.'.mp3'.'</a>';
}


}

if (isset($_POST['delete_zip'])) {
    foreach(glob('zip/*') as $zip) {
        unlink($zip);
    }
    
    $_msg = '<h2 style="color:blue">All downloaded files has been deleted!</h2>';
}

?>
<html>
<head><title>Soundcloud Downloader by (@rezamoradix)</title>
<style>/*!
 * Milligram v1.3.0
 * https://milligram.github.io
 *
 * Copyright (c) 2017 CJ Patoilo
 * Licensed under the MIT license
 */

*,*:after,*:before{box-sizing:inherit}html{box-sizing:border-box;font-size:62.5%}body{color:#606c76;font-family:'Roboto', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;font-size:1.6em;font-weight:300;letter-spacing:.01em;line-height:1.6}blockquote{border-left:0.3rem solid #d1d1d1;margin-left:0;margin-right:0;padding:1rem 1.5rem}blockquote *:last-child{margin-bottom:0}.button,button,input[type='button'],input[type='reset'],input[type='submit']{background-color:#9b4dca;border:0.1rem solid #9b4dca;border-radius:.4rem;color:#fff;cursor:pointer;display:inline-block;font-size:1.1rem;font-weight:700;height:3.8rem;letter-spacing:.1rem;line-height:3.8rem;padding:0 3.0rem;text-align:center;text-decoration:none;text-transform:uppercase;white-space:nowrap}.button:focus,.button:hover,button:focus,button:hover,input[type='button']:focus,input[type='button']:hover,input[type='reset']:focus,input[type='reset']:hover,input[type='submit']:focus,input[type='submit']:hover{background-color:#606c76;border-color:#606c76;color:#fff;outline:0}.button[disabled],button[disabled],input[type='button'][disabled],input[type='reset'][disabled],input[type='submit'][disabled]{cursor:default;opacity:.5}.button[disabled]:focus,.button[disabled]:hover,button[disabled]:focus,button[disabled]:hover,input[type='button'][disabled]:focus,input[type='button'][disabled]:hover,input[type='reset'][disabled]:focus,input[type='reset'][disabled]:hover,input[type='submit'][disabled]:focus,input[type='submit'][disabled]:hover{background-color:#9b4dca;border-color:#9b4dca}.button.button-outline,button.button-outline,input[type='button'].button-outline,input[type='reset'].button-outline,input[type='submit'].button-outline{background-color:transparent;color:#9b4dca}.button.button-outline:focus,.button.button-outline:hover,button.button-outline:focus,button.button-outline:hover,input[type='button'].button-outline:focus,input[type='button'].button-outline:hover,input[type='reset'].button-outline:focus,input[type='reset'].button-outline:hover,input[type='submit'].button-outline:focus,input[type='submit'].button-outline:hover{background-color:transparent;border-color:#606c76;color:#606c76}.button.button-outline[disabled]:focus,.button.button-outline[disabled]:hover,button.button-outline[disabled]:focus,button.button-outline[disabled]:hover,input[type='button'].button-outline[disabled]:focus,input[type='button'].button-outline[disabled]:hover,input[type='reset'].button-outline[disabled]:focus,input[type='reset'].button-outline[disabled]:hover,input[type='submit'].button-outline[disabled]:focus,input[type='submit'].button-outline[disabled]:hover{border-color:inherit;color:#9b4dca}.button.button-clear,button.button-clear,input[type='button'].button-clear,input[type='reset'].button-clear,input[type='submit'].button-clear{background-color:transparent;border-color:transparent;color:#9b4dca}.button.button-clear:focus,.button.button-clear:hover,button.button-clear:focus,button.button-clear:hover,input[type='button'].button-clear:focus,input[type='button'].button-clear:hover,input[type='reset'].button-clear:focus,input[type='reset'].button-clear:hover,input[type='submit'].button-clear:focus,input[type='submit'].button-clear:hover{background-color:transparent;border-color:transparent;color:#606c76}.button.button-clear[disabled]:focus,.button.button-clear[disabled]:hover,button.button-clear[disabled]:focus,button.button-clear[disabled]:hover,input[type='button'].button-clear[disabled]:focus,input[type='button'].button-clear[disabled]:hover,input[type='reset'].button-clear[disabled]:focus,input[type='reset'].button-clear[disabled]:hover,input[type='submit'].button-clear[disabled]:focus,input[type='submit'].button-clear[disabled]:hover{color:#9b4dca}code{background:#f4f5f6;border-radius:.4rem;font-size:86%;margin:0 .2rem;padding:.2rem .5rem;white-space:nowrap}pre{background:#f4f5f6;border-left:0.3rem solid #9b4dca;overflow-y:hidden}pre>code{border-radius:0;display:block;padding:1rem 1.5rem;white-space:pre}hr{border:0;border-top:0.1rem solid #f4f5f6;margin:3.0rem 0}input[type='email'],input[type='number'],input[type='password'],input[type='search'],input[type='tel'],input[type='text'],input[type='url'],textarea,select{-webkit-appearance:none;-moz-appearance:none;appearance:none;background-color:transparent;border:0.1rem solid #d1d1d1;border-radius:.4rem;box-shadow:none;box-sizing:inherit;height:3.8rem;padding:.6rem 1.0rem;width:100%}input[type='email']:focus,input[type='number']:focus,input[type='password']:focus,input[type='search']:focus,input[type='tel']:focus,input[type='text']:focus,input[type='url']:focus,textarea:focus,select:focus{border-color:#9b4dca;outline:0}select{background:url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" height="14" viewBox="0 0 29 14" width="29"><path fill="#d1d1d1" d="M9.37727 3.625l5.08154 6.93523L19.54036 3.625"/></svg>') center right no-repeat;padding-right:3.0rem}select:focus{background-image:url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" height="14" viewBox="0 0 29 14" width="29"><path fill="#9b4dca" d="M9.37727 3.625l5.08154 6.93523L19.54036 3.625"/></svg>')}textarea{min-height:6.5rem}label,legend{display:block;font-size:1.6rem;font-weight:700;margin-bottom:.5rem}fieldset{border-width:0;padding:0}input[type='checkbox'],input[type='radio']{display:inline}.label-inline{display:inline-block;font-weight:normal;margin-left:.5rem}.container{margin:0 auto;max-width:112.0rem;padding:0 2.0rem;position:relative;width:100%}.row{display:flex;flex-direction:column;padding:0;width:100%}.row.row-no-padding{padding:0}.row.row-no-padding>.column{padding:0}.row.row-wrap{flex-wrap:wrap}.row.row-top{align-items:flex-start}.row.row-bottom{align-items:flex-end}.row.row-center{align-items:center}.row.row-stretch{align-items:stretch}.row.row-baseline{align-items:baseline}.row .column{display:block;flex:1 1 auto;margin-left:0;max-width:100%;width:100%}.row .column.column-offset-10{margin-left:10%}.row .column.column-offset-20{margin-left:20%}.row .column.column-offset-25{margin-left:25%}.row .column.column-offset-33,.row .column.column-offset-34{margin-left:33.3333%}.row .column.column-offset-50{margin-left:50%}.row .column.column-offset-66,.row .column.column-offset-67{margin-left:66.6666%}.row .column.column-offset-75{margin-left:75%}.row .column.column-offset-80{margin-left:80%}.row .column.column-offset-90{margin-left:90%}.row .column.column-10{flex:0 0 10%;max-width:10%}.row .column.column-20{flex:0 0 20%;max-width:20%}.row .column.column-25{flex:0 0 25%;max-width:25%}.row .column.column-33,.row .column.column-34{flex:0 0 33.3333%;max-width:33.3333%}.row .column.column-40{flex:0 0 40%;max-width:40%}.row .column.column-50{flex:0 0 50%;max-width:50%}.row .column.column-60{flex:0 0 60%;max-width:60%}.row .column.column-66,.row .column.column-67{flex:0 0 66.6666%;max-width:66.6666%}.row .column.column-75{flex:0 0 75%;max-width:75%}.row .column.column-80{flex:0 0 80%;max-width:80%}.row .column.column-90{flex:0 0 90%;max-width:90%}.row .column .column-top{align-self:flex-start}.row .column .column-bottom{align-self:flex-end}.row .column .column-center{-ms-grid-row-align:center;align-self:center}@media (min-width: 40rem){.row{flex-direction:row;margin-left:-1.0rem;width:calc(100% + 2.0rem)}.row .column{margin-bottom:inherit;padding:0 1.0rem}}a{color:#9b4dca;text-decoration:none}a:focus,a:hover{color:#606c76}dl,ol,ul{list-style:none;margin-top:0;padding-left:0}dl dl,dl ol,dl ul,ol dl,ol ol,ol ul,ul dl,ul ol,ul ul{font-size:90%;margin:1.5rem 0 1.5rem 3.0rem}ol{list-style:decimal inside}ul{list-style:circle inside}.button,button,dd,dt,li{margin-bottom:1.0rem}fieldset,input,select,textarea{margin-bottom:1.5rem}blockquote,dl,figure,form,ol,p,pre,table,ul{margin-bottom:2.5rem}table{border-spacing:0;width:100%}td,th{border-bottom:0.1rem solid #e1e1e1;padding:1.2rem 1.5rem;text-align:left}td:first-child,th:first-child{padding-left:0}td:last-child,th:last-child{padding-right:0}b,strong{font-weight:bold}p{margin-top:0}h1,h2,h3,h4,h5,h6{font-weight:300;letter-spacing:-.1rem;margin-bottom:2.0rem;margin-top:0}h1{font-size:4.6rem;line-height:1.2}h2{font-size:3.6rem;line-height:1.25}h3{font-size:2.8rem;line-height:1.3}h4{font-size:2.2rem;letter-spacing:-.08rem;line-height:1.35}h5{font-size:1.8rem;letter-spacing:-.05rem;line-height:1.5}h6{font-size:1.6rem;letter-spacing:0;line-height:1.4}img{max-width:100%}.clearfix:after{clear:both;content:' ';display:table}.float-left{float:left}.float-right{float:right}

/*# sourceMappingURL=milligram.min.css.map */</style>
</head>

<body class="container">
    <h2>Soundcloud Downloader <span style="font-size:12px">by</span> <a href="https://github.com/rezamoradix" target="_blank" style="font-size:12px">(@rezamoradix)</a> </h2>
    
    <form  method="post">
    <input type="text" name="add" style="font-size:20px; width: 50%;">
    <input type="submit" value="DOWNLOAD" style="font-size:20px; background:#ff0075;color:white;border:0;">
    </form>

    <div style="margin:5px 0">
        <?php echo isset($_msg) ? $_msg : '' ?>
    </div>

    <form  method="post">
    <input name="delete_zip" style="font-size:20px; width: 50%;" type="hidden">
    <input type="submit" value="DELETE ALL FILES" style="font-size:20px; background:#ffaa75;color:white;border:0;">
    </form>

    <?php

    foreach(glob('zip/*') as $file)
    {
        $size = floor((filesize($file) / 1024) / 1024) . 'MB';
        echo '<a style="font-size:20px; font-family:calibri; padding: 10px 15px; display:block;" href="'.$file.'">'.$file.' -- '.$size.'</a>';
    }

    ?>
</body>

</html>