<?php
header('Access-Control-Allow-Origin: *');
include('../vendor/autoload.php');
use Asika\Pdf2text;

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
            $list[] = ["id" => $name[0],"name" => $file, "url" => str_replace("../","",$router), 'isdir' => $esfolder];
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
            $list[] = ["id" => $name[0],"name" => $file, "url" => str_replace("../","",$router), 'isdir' => $esfolder];
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

$acentos = array("Ñ","á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
$noacentos = array("N","a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");
$buscar = strtoupper(str_replace($acentos, $noacentos, $buscar));

$arr = ["ruta" => $base];
if(is_dir($localrute)){
    $arr['dir'] = true;
    $folders = scandir($localrute); //shell_exec('find ' . $localrute . ' -name ' . $buscar);
    $resultado_busqueda = [];
    $list = [];
    $files = mostrar_carpeta($localrute);
    /*
    if($buscar!=""){
        foreach($files as $i => $file){
            if($file != '.' && $file != '..' && $file != '.htaccess'){
                $dir = $file;
                if(strpos(strtoupper(str_replace($acentos, $noacentos, $file['name'])),$buscar)){
                    $resultado_busqueda[$i] = $file;
                }
                
            }
        }
    }*/

    $reader = new \Asika\Pdf2text;
    
    if($buscar!=""){
        foreach($files as $i => $file){
            if($file != '.' && $file != '..' && $file != '.htaccess'){
                $dir = is_dir("../".$file['url']);
                if(!$dir){
                    $resultado_busqueda[] = $file ;
                    if(strpos($file['name'],".pdf")){
                        $resultado_busqueda[$i] = $file;
                        $output = $reader->decode("../".$file['url']);
                        $output = str_replace($acentos, $noacentos, $output);
                        if(strpos(strtoupper($output), $buscar)){
                            $resultado_busqueda[$i] = $file;
                        }
                    }
                }
            }
        }
    }
    //$resultado_busqueda
    asort($resultado_busqueda);
    $newfolder = [];
    foreach($resultado_busqueda as $i => $value){ $newfolder[] = $value; }

    //
    //$output = $reader->decode($fileName);

    $arr["files"] = $folders;
    $arr["folder"] = $newfolder;
    $arr["result"] = $resultado_busqueda;
}else{
    $arr['dir'] = false;
}
print_r(json_encode($arr));
