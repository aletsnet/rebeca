<?php
$data = json_decode(file_get_contents("php://input"), true);
$base = "files";
$ruta = $data["dir"];
$base = ($ruta != "" ? $ruta : $base);
$localrute = getcwd() . "/../" . $base;
$arr = ["ruta" => $base];
if(is_dir($localrute)){
    if($dir = opendir($localrute)){
        $arr['dir'] = true;
        $file = [];
        $folder = [];
        //asort($file);
        while(($archivo = readdir($dir)) !== false){
            if($archivo != '.' && $archivo != '..' && $archivo != '.htaccess'){
                $isdir = is_dir($localrute."/".$archivo);
                $file[] = ["name" => $archivo, "url" => $base."/".$archivo, "isdir" => $isdir];
                if($isdir){
                    $folder[] = ["name" => $archivo, "url" => $base."/".$archivo];
                }
            }
        }
        closedir($dir);
        //asort($file);
        $arr['files'] = $file;
        //asort($folder);
        $arr['folder'] = $folder;
    }
}else{
    $arr['dir'] = false;
}

print_r(json_encode($arr));