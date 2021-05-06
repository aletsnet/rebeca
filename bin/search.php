<?php
header('Access-Control-Allow-Origin: *');

function mostrar_carpeta($carpeta){
    $list = [];
    $files = scandir($carpeta);
    foreach($files as $i => $file){
        if($file != '.' && $file != '..' && $file != '.htaccess'){
            $name = explode(" ", $file);
            $router = $carpeta . "/".$file;
            $subcarpeta = [];
            $esfolder = is_dir($router);
            if($esfolder){
                $lista = mostrar_subcarpeta($router);
                $list = array_merge($list, $lista);
            }
            $list[] = ["id" => $name[0],"name" => $file, "url" => $router, 'isdir' => $esfolder];
        }
    }
    return $list;
}

function mostrar_subcarpeta($carpeta){
    $list = [];
    $files = scandir($carpeta);
    foreach($files as $i => $file){
        if($file != '.' && $file != '..' && $file != '.htaccess'){
            $name = explode(" ", $file);
            $router = $carpeta . "/".$file;
            $subcarpeta = [];
            $esfolder = is_dir($router);
            if($esfolder){
                $lista = mostrar_carpeta($router);
                $list = array_merge($list, $lista);
            }
            $list[] = ["id" => $name[0],"name" => $file, "url" => $router, 'isdir' => $esfolder];
        }
    }
    return $list;
}

$data = json_decode(file_get_contents("php://input"), true);
$base = "files";
$ruta = $data["dir"];
$buscar = trim($data["buscar"]);
$base = ($ruta != "" ? $ruta : $base);
$localrute =  "../" . $base;

$arr = ["ruta" => $base];
if(is_dir($localrute)){
    $arr['dir'] = true;
    $folders = scandir($localrute); //shell_exec('find ' . $localrute . ' -name ' . $buscar);
    $resultado_busqueda = [];
    $list = [];
    $files = mostrar_carpeta($localrute);
    if($buscar!=""){
        foreach($files as $i => $file){
            if($file != '.' && $file != '..' && $file != '.htaccess'){
                $dir = $file;
                if(strpos(strtolower($file['name']),strtolower($buscar))){
                    $resultado_busqueda[$i] = $file;
                }else{
                    if(strpos(strtoupper($file['name']),strtoupper($buscar))){
                        $resultado_busqueda[$i] = $file;
                    }
                }
                
            }
        }
    }
    //$resultado_busqueda
    asort($resultado_busqueda);
    $newfolder = [];
    foreach($resultado_busqueda as $i => $value){ $newfolder[] = $value; }

    $arr["files"] = $folders;
    $arr["folder"] = $newfolder;
    $arr["result"] = $resultado_busqueda;
}else{
    $arr['dir'] = false;
}
print_r(json_encode($arr));