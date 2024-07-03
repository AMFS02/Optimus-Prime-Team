<script>
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open("GET", "motor_ajax.php?q=" + 'man', true);
    xmlhttp.send()
</script>

<script>
    var bt = 0;
    // Setting Initial Position
    var iniPos1 = 0; var iniPos2 = 25; var iniPos3 = 0;
    
    function eletro(){
      var botao = document.getElementById('botao'); var iman = document.getElementById('iman');

      if(bt == 0) {
          bt = 1; botao.value = 'ON';
          iman.src = 'imagens/iman.png';
      }
      else { 
        bt = 0; botao.value = 'OFF';
        iman.src = 'imagens/imanbw.png';
      }
    }

    function servo_ajax(str) {
        var s1 = document.getElementById('servo1');
        var s2 = document.getElementById('servo2');
        var s3 = document.getElementById('servo3');
        var x;

        var p1, p2, p3;
        if(str == 'seguranca' || str == 'depositar'){ 
            if(str == 'seguranca'){
               p1 = iniPos1; p2 = iniPos2; p3 = iniPos3; bt = 0; 
            }
            else {
                p1 = 100; p2 = 100; p3 = 100; bt = 1; 
            }
            document.getElementById('vservo1').innerHTML = p1; 
            document.getElementById('vservo2').innerHTML = p2;
            document.getElementById('vservo3').innerHTML = p3;
            
            s1.value = p1; 
            s2.value = p2; 
            s3.value = p3; 
        } 
        else {
            var servo = document.getElementById(str);
            var vservo = document.getElementById("v"+str)
            vservo.innerHTML = servo.value;

        }  
        x = 'sr;'+ s1.value +';'+ s2.value +';'+ s3.value +';'+ bt; 

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.open("GET", "motor_ajax.php?q=" + (x), true);
        xmlhttp.send();
    }
</script>

<h2>Braço - Navegação Direta</h2>

<div class="ca57">
    <div class="limite">
        <?php include_once('webcam.php'); ?>
    </div>
</div>

<div class="ca75">
  <div class="slidecontainer">
    <p><input type="range" min="1" max="180" value="0" class="slider" id="servo1" oninput="servo_ajax('servo1')">
    <br>Base (Servo 1): <span id="vservo1">0</span>º</p>

    <p><input type="range" min="1" max="120" value="25" class="slider" id="servo2" oninput="servo_ajax('servo2')">
    <br>Articulação 1 (Servo 2): <span id="vservo2">25</span>º</p>

    <p><input type="range" min="1" max="180" value="0" class="slider" id="servo3" oninput="servo_ajax('servo3')">
    <br>Articulação 2 (Servo 3): <span id="vservo3">0</span>º</p>

    <p><input type="button" style="width:50%;" id="seguranca" onclick="servo_ajax('seguranca')" value=" Posição de Segurança "></p>
    <p><input type="button" style="width:50%;" id="depositar" onclick="servo_ajax('depositar')" value=" Depositar "></p>

    <input type="button" style="width:40%;" id="botao" onclick="eletro(); servo_ajax('servo1')" value=" OFF "> &nbsp;&nbsp;
    <img id="iman" src="imagens/imanbw.png" alt="" style="width:50px;">
     
  </div>
</div>