<?php
    $arq = 'motor.txt';

    if(isset($_GET['q']) and trim($_GET['q']) != ''){
        $valor =  trim($_GET['q']);
        // if( file_exists($arq) ){ unlink($arq); }
        $arquivo = fopen($arq,'w');
        fwrite($arquivo, $valor);    
        fclose($arquivo);
    }

?>