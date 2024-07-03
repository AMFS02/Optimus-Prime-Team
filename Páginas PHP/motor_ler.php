<?php
    $arq = 'motor.txt';

    if( file_exists($arq) ){
        $arquivo = fopen($arq,'r');
        while( $linha = fgets( $arquivo )){
            print trim($linha);
        }
        fclose($arquivo);
    }
?>