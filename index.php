<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Lector RSS PHP - Vercel</title>
        <style>
            body { font-family: sans-serif; background-color: #f4f4f4; padding: 20px; }
            form { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 30px; }
            fieldset { border: 1px solid #ddd; padding: 15px; border-radius: 5px; }
            legend { font-weight: bold; color: #004488; }
            label { margin-right: 10px; font-weight: 500; }
            select, input { padding: 5px; margin-right: 20px; border: 1px solid #ccc; border-radius: 4px; }
            input[type="submit"] { background-color: #004488; color: white; border: none; padding: 8px 15px; cursor: pointer; }
            input[type="submit"]:hover { background-color: #002a55; }
            h2 { text-align: center; color: #333; }
        </style>
    </head>
    <body>
        
        <form action="index.php" method="GET">
            <fieldset> 
                <legend>FILTRO DE NOTICIAS</legend>
                
                <label>PERIODICO : </label>
                <select name="periodicos">
                    <option value="elpais" <?php if(isset($_GET['periodicos']) && $_GET['periodicos'] == 'elpais') echo 'selected'; ?>>El Pais</option>
                    <option value="elmundo" <?php if(isset($_GET['periodicos']) && $_GET['periodicos'] == 'elmundo') echo 'selected'; ?>>El Mundo</option>      
                </select> 
                
                <label>CATEGORIA : </label>
                <select name="categoria">
                    <option value=""></option>
                    <option value="Política">Política</option>
                    <option value="Deportes">Deportes</option>
                    <option value="Ciencia">Ciencia</option>
                    <option value="España">España</option>
                    <option value="Economía">Economía</option>
                    <option value="Música">Música</option>
                    <option value="Cine">Cine</option>
                    <option value="Europa">Europa</option>
                    <option value="Justicia">Justicia</option>                 
                </select>

                <label>FECHA : </label>
                <input type="date" name="fecha">

                <label>BUSCAR : </label>
                <input type="text" name="buscar" placeholder="Palabra clave...">
                
                <input type="submit" name="filtrar" value="Ver Noticias">
            </fieldset>
        </form>
        
        <div id="contenedor-noticias">
        <?php
            $periodicoSeleccionado = isset($_REQUEST['periodicos']) ? $_REQUEST['periodicos'] : 'elpais';

            if ($periodicoSeleccionado == 'elmundo') {
                echo "<h2>Noticias de El Mundo</h2>";
                
                require_once "RSSElMundo.php"; 

            } else {
                echo "<h2>Noticias de El País</h2>";
                
                require_once "RSSElPais.php";
            }
        ?>
        </div>
        
    </body>
</html>
