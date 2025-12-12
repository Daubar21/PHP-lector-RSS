<?php

$url = "https://e00-elmundo.uecdn.es/elmundo/rss/portada.xml";

$opciones = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n"
    ]
];

$contexto = stream_context_create($opciones);
$sXML = file_get_contents($url, false, $contexto);

if ($sXML === FALSE || empty($sXML)) {
    echo "<p style='color:red; text-align:center;'>Error: No se han podido cargar las noticias de El Mundo.</p>";
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
        $descripcion = strip_tags($item->description);
        $fecha = date('d/m/Y', strtotime($item->pubDate));
        
        $imagenHTML = "";
        
        $media = $item->children('http://search.yahoo.com/mrss/');
        
        if (isset($media->content) && isset($media->content->attributes()->url)) {
            $imgUrl = (string)$media->content->attributes()->url;
            $imagenHTML = "<img src='$imgUrl' style='width:100%; height:auto; border-radius:5px 5px 0 0;'>";
        } elseif (isset($media->thumbnail) && isset($media->thumbnail->attributes()->url)) {
            $imgUrl = (string)$media->thumbnail->attributes()->url;
            $imagenHTML = "<img src='$imgUrl' style='width:100%; height:auto; border-radius:5px 5px 0 0;'>";
        } elseif (isset($item->enclosure) && isset($item->enclosure['url'])) {
             $imgUrl = (string)$item->enclosure['url'];
             $imagenHTML = "<img src='$imgUrl' style='width:100%; height:auto; border-radius:5px 5px 0 0;'>";
        }
        echo "
        <article style='border: 1px solid #ddd; padding: 0; width: 300px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); background: white; border-radius: 8px; overflow: hidden; display:flex; flex-direction:column;'>
            $imagenHTML
            <div style='padding: 15px;'>
                <h3 style='font-size: 1.1em; margin: 0 0 10px 0;'><a href='$linkNoticia' target='_blank' style='text-decoration:none; color:#2c3e50;'>$titulo</a></h3>
                <p style='font-size: 0.75em; color: #999; margin-bottom: 10px;'>$fecha</p>
                <p style='font-size: 0.9em; line-height: 1.4; color: #555;'>$descripcion</p>
                <a href='$linkNoticia' target='_blank' style='display:inline-block; margin-top:10px; padding:8px 15px; background:#2c3e50; color:white; text-decoration:none; border-radius:4px; font-size:0.8em;'>Leer noticia completa</a>
            </div>
        </article>
        ";
    }
    echo '</div>';
}
?>
