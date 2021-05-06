<?php
header('Access-Control-Allow-Origin: *');

$data = json_decode(file_get_contents("php://input"), true);
$base = "files";
$ruta = $data["dir"];
$base = ($ruta != "" ? $ruta : $base);
$localrute = "../" . $base;
$arr = ["ruta" => $base];
if(is_dir($localrute)){
    if($dir = opendir($localrute)){
        $arr['dir'] = true;
        $file = [];
        $folder = [];
        //asort($file);
        while(($archivo = readdir($dir)) !== false){
            if($archivo != '.' && $archivo != '..' && $archivo != '.htaccess'){
                $url_desglose = explode(" ", $archivo);
                $indice = str_replace(".","",$url_desglose[0]);
                $isdir = is_dir($localrute."/".$archivo);
                $file[] = ["indice" => $indice, "name" => $archivo, "url" => $base."/".$archivo, "isdir" => $isdir];
                if($isdir){
                    $folder[] = ["indice" => $indice, "name" => $archivo, "url" => $base."/".$archivo];
                }
            }
        }
        closedir($dir);
        asort($file);
        $newfiles = [];
        foreach($file as $i => $value){ $newfiles[] = $value; }
        $arr['files'] = $newfiles;

        $subcarpetas = explode("/",$localrute);
        $router = "";
        $list_foler = [];
        foreach($subcarpetas as $i => $subfoler){
            $router .= ($router!=""?"/":"") . $subfoler;
            if($subfoler != ".." && $subfoler != "." && $subfoler != "files"){
                $temp = scandir($router);
                foreach($temp as $i => $v){
                    if($v != ".." && $v != "." && $v != ".htaccess"){
                        $temp_name = explode(" ", $v);
                        $list_foler[] = ["indice" => $temp_name[0], "name" => $v, "url" => $router."/".$v];; 
                    }
                }
            }
        }

        asort($folder);
        $newfolder = [];
        foreach($folder as $i => $value){ $newfolder[] = $value; }
        $arr['folder'] = $list_foler;
    }
}else{
    $arr['dir'] = false;
}

print_r(json_encode($arr));