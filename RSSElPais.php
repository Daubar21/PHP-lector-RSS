<?php

$url = "http://ep00.epimg.net/rss/elpais/portada.xml";

$opciones = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n"
    ]
];

$contexto = stream_context_create($opciones);

$sXML = file_get_contents($url, false, $contexto);

if ($sXML === FALSE || empty($sXML)) {
    die("Error crítico: No se ha podido descargar el XML. El servidor remoto ha rechazado la conexión.");
}

try {
    $oXML = new SimpleXMLElement($sXML);
} catch (Exception $e) {
    die("Error al procesar el XML: " . $e->getMessage());
}

require_once "conexionBBDD.php";

if(isset($link) && mysqli_connect_error()){
    printf("Conexión a la base de datos ha fallado: %s\n", mysqli_connect_error());
} elseif (isset($link)) {
        
    $contador = 0;
    $categoria = ["Política","Deportes","Ciencia","España","Economía","Música","Cine","Europa","Justicia"];
    $categoriaFiltro = "";
        
    if (isset($oXML->channel->item)) {
        foreach ($oXML->channel->item as $item){
            
            if (isset($item->category)) {
                for ($i=0; $i<count($item->category); $i++){ 
                    for($j=0; $j<count($categoria); $j++){
                        if((string)$item->category[$i] == $categoria[$j]){
                            $categoriaFiltro = "[".$categoria[$j]."]".$categoriaFiltro;
                        }
                    } 
                }
            }

            $fPubli = strtotime($item->pubDate);
            $new_fPubli = date('Y-m-d', $fPubli);
            
            $content = $item->children("content", true);
            $encoded = isset($content->encoded) ? $content->encoded : ""; 

            $sql = "SELECT link FROM elpais";
            $result = mysqli_query($link, $sql); 
            
            $Repit = false;

            if ($result) {
                while($sqlCompara = mysqli_fetch_array($result)){
                    if($sqlCompara['link'] == $item->link){
                        $Repit = true; 
                        $contador++;
                        $contadorTotal = $contador;
                        break;
                    }
                }
            }

            if($Repit == false && $categoriaFiltro <> ""){
                $tituloSafe = mysqli_real_escape_string($link, $item->title);
                $descSafe = mysqli_real_escape_string($link, $item->description);
                $linkSafe = mysqli_real_escape_string($link, $item->link);
                $encodedSafe = mysqli_real_escape_string($link, $encoded);
                
                $sql = "INSERT INTO elpais VALUES('','$tituloSafe','$linkSafe','$descSafe','$categoriaFiltro','$new_fPubli','$encodedSafe')";
                mysqli_query($link, $sql);
            } 
            
            $categoriaFiltro = "";
        }
    }
} else {
    echo "Error: La variable de conexión \$link no está definida.";
}
?>
