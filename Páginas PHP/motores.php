<script>
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open("GET", "motor_ajax.php?q=" + 'man', true);
    xmlhttp.send()
</script>

<script>
    function motor_ajax(str) {
      if (str.length >= 0){
        var velo = document.getElementById(str);

            if(str == 'velo'){
                document.getElementById('vvelo').innerHTML = velo.value;

                var xmlhttp = new XMLHttpRequest();
                xmlhttp.open("GET", "motor_ajax.php?q=" + 'sr;' + 'velo;' + velo.value, true);
                xmlhttp.send();

            } else {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.open("GET", "motor_ajax.php?q=" + str, true);
                xmlhttp.send();
            }

        }
    }
</script>

<h2>Navegação</h2>

<div class="ca57">
    <div class="limite">
        <?php include_once('webcam.php'); ?>
    </div>
</div>

<div class="ca75">
    <div class="limite">
        <div class="setas c"> <img src="imagens/left.png"  onclick ="motor_ajax('btesquerda')" onmouseout="motor_ajax('btparar')">Left</div>
        <div class="setas c"> <img src="imagens/up.png"    onclick="motor_ajax('btcima')"      onmouseout="motor_ajax('btparar')">Forward</div>
        <div class="setas c"> <img src="imagens/down.png"  onclick="motor_ajax('btbaixo')"     onmouseout="motor_ajax('btparar')">Backwards</div>
        <div class="setas c"> <img src="imagens/right.png" onclick="motor_ajax('btdireita')"   onmouseout="motor_ajax('btparar')">Right</div>
        <div class="limpar"></div></p>
        <div class="slidecontainer l", left>&nbsp;
            <p><input type="range"  min="0" max="100" value="50" class="slider"id="velo" oninput="motor_ajax('velo')">
            <br>Velocidade: <span id="vvelo">50</span>% </p>
        </div>
    </div>
</div>

<p>&nbsp;</p>