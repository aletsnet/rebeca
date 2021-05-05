<?php
header('Access-Control-Allow-Origin: *');

function mostrar($carpeta){
    $list = [];
    $files = scandir($localrute);
    foreach($files as $i => $file){
        if($file != '.' && $archivo != '..' && $archivo != '.htaccess'){
            $dir = $file;
            //$resultado_busqueda[$i] = $file;
            $subcarpeta = [];
            if(is_dir($localrute . "/".$dir)){
                $subcarpeta = scandir($localrute . "/".$dir);
                foreach($files as $i => $file){
    
                }
            }
            $resultado_busqueda[$i] = $file;
        }
    }
    
    return $list;
}

$data = json_decode(file_get_contents("php://input"), true);
$base = "files";
$ruta = $data["dir"];
$buscar = $data["buscar"];
$base = ($ruta != "" ? $ruta : $base);
$localrute =  "../" . $base;

$arr = ["ruta" => $base, "buscar" => $buscar];

$files = scandir($localrute); //shell_exec('find ' . $localrute . ' -name ' . $buscar);
$resultado_busqueda = [];
$list = [];
foreach($files as $i => $file){
    if($file != '.' && $archivo != '..' && $archivo != '.htaccess'){
        $dir = $file;
        if(strpos($file,$buscar)){
            $resultado_busqueda[$i] = $file;
        }
        $list[] = strpos($file,$buscar) ."|". $file ."|". $buscar;
    }
}

$arr["files"] = $list;
$arr["resultado"] = $resultado_busqueda;
print_r(json_encode($arr));