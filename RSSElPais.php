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
    echo "<p style='color:red'>No se han podido cargar las noticias de El País.</p>";
    $oXML = false;
} else {
    libxml_use_internal_errors(true);
    $oXML = simplexml_load_string($sXML);
}

if ($oXML) {
    echo '<div style="display:flex; flex-wrap:wrap; gap: 20px; justify-content: center;">';
    
    foreach ($oXML->channel->item as $item){
        $titulo = $item->title;
        $linkNoticia = $item->link;
        $descripcion = $item->description;
        $fecha = date('d/m/Y', strtotime($item->pubDate));
        
        $imagen = "";
        $content = $item->children("content", true); 
        if (isset($content->encoded)) {
             preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $content->encoded, $image);
             if(isset($image['src'])){
                 $imagen = "<img src='{$image['src']}' style='width:100%; height:auto; border-radius:5px;'>";
             }
        }
        
        echo "
        <article style='border: 1px solid #ddd; padding: 15px; width: 300px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); background: white; border-radius: 8px;'>
            $imagen
            <h3 style='font-size: 1.1em; margin-top: 10px;'><a href='$linkNoticia' target='_blank' style='text-decoration:none; color:#004488;'>$titulo</a></h3>
            <p style='font-size: 0.8em; color: #666;'>$fecha</p>
            <p style='font-size: 0.9em; line-height: 1.4;'>$descripcion</p>
            <a href='$linkNoticia' target='_blank' style='display:inline-block; margin-top:10px; padding:5px 10px; background:#004488; color:white; text-decoration:none; border-radius:4px; font-size:0.8em;'>Leer más</a>
        </article>
        ";
    }
    echo '</div>';
}
?>
