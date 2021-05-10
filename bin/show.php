<?php
header('Access-Control-Allow-Origin: *');

$data = json_decode(file_get_contents("php://input"), true);
$base = "files";
$ruta = str_replace("../","",$data["dir"]);
$base = ($ruta != "" ? $ruta : $base);
$localrute = "../" . $base;
$arr = ["ruta" => $ruta];
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
        foreach($file as $i => $value){
            if(!strpos($file['name'],'NORMAS DE USO COMUN')){
                $newfiles[] = $value; 
            }
        }
        $arr['files'] = $newfiles;

        $subcarpetas = explode("/",$localrute);
        $router = "";
        $list_foler = [];
        foreach($subcarpetas as $i => $subfoler){
            $router .= ($router!=""?"/":"") . $subfoler;
            if($subfoler != ".." && $subfoler != "." ){
                $temp = scandir($router);
                foreach($temp as $i => $v){
                    if($v != ".." && $v != "." && $v != ".htaccess"){
                        if(is_dir($router."/".$v)){
                            $temp_name = explode(" ", $v);
                            $list_foler[] = ["indice" => (float) $temp_name[0], "name" => $v, "url" => str_replace("../","",($router."/".$v))]; 
                        }
                    }
                }
            }
        }

        asort($list_foler);
        $newfolder = [];
        foreach($list_foler as $i => $value){ 
            if(!strpos($file['name'],'NORMAS DE USO COMUN')){
                $newfolder[] = $value;
            }
        }
        $arr['folder'] = $newfolder;
        $arr['result'] = $newfolder;
        ;
    }
}else{
    $arr['dir'] = false;
}

print_r(json_encode($arr));