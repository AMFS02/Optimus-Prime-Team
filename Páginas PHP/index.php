<?php 
    $msg = '';
    $util = 0;

    # Labels das Páginas do site 
    $pagina = array('inicio', 'joystick', 'braco_direta', 'braco_inversa', 'automatico');
    
    # Títulos das páginas do site
    $titulo = array('Controlo Carro - Botões', 'Controlo Carro - Joystick', 'Braço - Cinemática Direta','Braço - Cinemática Inversa', 'Automático');

    date_default_timezone_set('Europe/Lisbon');

?>
<!-- Estrutura HTML - BoilerPlate -->
<!DOCTYPE html> 
<html lang="pt-pt">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="boilerplate.css" rel="stylesheet" type="text/css">
  <link href="estrutura.css" rel="stylesheet" type="text/css">
  <link href="estilos.css" rel="stylesheet" type="text/css">
  <link rel='icon' type='image/png' href='imagens/favicon.png' >
  <script src="respond.min.js"></script>
  <title>Optimus</title>
</head>
<body>
  
  <!-- Definição do Layout em Grelha  -->
  <div class="gridContainer clearfix">

    <!-- Banner do Site  
    <div class="c"><img src="imagens/banner.jpg" alt=""></div>
-->
      <!-- Menu de navegação  -->
      <div class="c11"><div class="cx">
        <?php
          for($i=0; $i<count($pagina); $i++){
            print ' <a href="index.php?p='.$pagina[$i].'">'.$titulo[$i].'</a>';
            ($i+1<count($pagina)) ? print ' | ': '';
          }
        ?>
      </div>
    </div>
  
    <?php
              
      # Aceder a uma página no menu de navegação
      if(isset($_GET['p']) and in_array($_GET['p'], $pagina) and file_exists($_GET['p'].'.php')){
          $defeito = trim($_GET['p']);
      }
      #Página de Início  Base 
      else {
          $defeito = 'motores';
      }

      include_once($defeito.'.php');
    
      ?>
    
    <p>&nbsp;</p>
    <p>&nbsp;</p>
  </div>

    <!-- Nota de Rodapé com Moto e Data  -->
    <div class="c11" class="l">&nbsp;
        <div id="fundo" class="fundo c pd25">Optimus @ <?php print date('Y'); ?></div>
    </div>
  
</body>
</html>
