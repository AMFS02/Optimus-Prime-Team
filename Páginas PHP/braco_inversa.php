<script>
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open("GET", "motor_ajax.php?q=" + 'man', true);
    xmlhttp.send()
</script>

<script>
    var ARM_LENGTH_1 = 120; var ARM_LENGTH_2 = 100; var ARM_LENGTH_3 = 40;

    // valores de repouso do braço (hardware)          : s1: 0; s2: 25; s3:0;
    // valores de repouso desta página (inverso)       : s1: 0; s2: 45; s3:190;

    // valores do braço esticado (hardware)            : s1: 0; s2: 60; s3:145;
    // valores do braço esticado desta página (inverso): s1: 0; s2: 90; s3:90;

    // valores do braço no chão (hardware)            : s1: 0; s2: 115; s3:70;
    // valores do braço no chão desta página (inverso): s1: 0; s2: 150; s3: 245;

    var arm_angles = [30, 150, 270]; 
    var base_angle = 0;

    var canvas_width = 450; var canvas_height = 360;

    var electromagnet_state = 0;
    var deposit_button_state = 0;

    var click_state = 0; var touch_state = 0;
    var mouse_start_x = 0; var mouse_start_y = 0;
    var mouse_current_x = 0; var mouse_current_y = 0;
    var mouse_move_threshold = 5;

    var canvas, ctx;

    function init() {
        canvas = document.getElementById("canvas");
        canvas.width = canvas_width;
        canvas.height = canvas_height;

        ctx = canvas.getContext("2d");

        canvas.addEventListener("mousedown", mouseDown);
        canvas.addEventListener("mousemove", mouseMove);
        canvas.addEventListener("mouseup", mouseUp);

        canvas.addEventListener("touchstart", touchStart);
        canvas.addEventListener("touchmove", touchMove);
        canvas.addEventListener("touchend", touchEnd);

        updateView();
    }

    function servo_base(){
        // Update slider value display
        var base_Slider = document.getElementById("base_Slider");
        var baseValue = document.getElementById("baseValue");

        base_angle = parseInt(base_Slider.value);
        baseValue.innerHTML = base_angle;
        
        updateView();
    }

    function eletro(){
        // Update switch value display
        var electromagnetSwitch = document.getElementById("electromagnetSwitch");
        var electromagnetValue = document.getElementById("electromagnetValue");
    
        electromagnetSwitch.checked ? electromagnet_state = 1 : electromagnet_state = 0;
        electromagnetValue.innerHTML = electromagnet_state;

        updateView();
    }

    function updateView() {
        ctx.clearRect(0, 0, canvas_width, canvas_height);

        // Draw Arm Joints
        var joint_radius = 15;
        ctx.fillStyle = "#FFFFFF";
        ctx.beginPath();
        ctx.arc(canvas_width / 2, canvas_height / 1.30, joint_radius, 0, Math.PI * 2);
        ctx.fill();

        var arm_end_x = canvas_width / 2 + ARM_LENGTH_1 * Math.cos(arm_angles[0] * Math.PI / 180);
        var arm_end_y = canvas_height / 1.30 - ARM_LENGTH_1 * Math.sin(arm_angles[0] * Math.PI / 180);

        ctx.beginPath();
        ctx.arc(arm_end_x, arm_end_y, joint_radius, 0, Math.PI * 2);
        ctx.fill();

        var arm_2_end_x = arm_end_x + ARM_LENGTH_2 * Math.cos((arm_angles[0] + arm_angles[1]) * Math.PI / 180);
        var arm_2_end_y = arm_end_y - ARM_LENGTH_2 * Math.sin((arm_angles[0] + arm_angles[1]) * Math.PI / 180);

        ctx.beginPath();
        ctx.arc(arm_2_end_x, arm_2_end_y, joint_radius, 0, Math.PI * 2);
        ctx.fill();

        var arm_3_end_x = arm_2_end_x + ARM_LENGTH_3 * Math.cos(arm_angles[2] * Math.PI / 180);
        var arm_3_end_y = arm_2_end_y - ARM_LENGTH_3 * Math.sin(arm_angles[2] * Math.PI / 180);

        var joint_radius = 15;
        ctx.fillStyle = "#FFFFFF";
        ctx.beginPath();
        ctx.arc(arm_2_end_x, arm_2_end_y, joint_radius, 0, Math.PI * 2);
        ctx.fill();

        // Draw Arm Segments
        ctx.strokeStyle = "#FFFFFF";
        ctx.lineWidth = 12;
        ctx.beginPath();
        ctx.moveTo(canvas_width / 2, canvas_height / 1.30);
        ctx.lineTo(arm_end_x, arm_end_y);
        ctx.stroke();

        ctx.beginPath();
        ctx.moveTo(arm_end_x, arm_end_y);
        ctx.lineTo(arm_2_end_x, arm_2_end_y);
        ctx.stroke();

        ctx.strokeStyle = "#157206";
        ctx.lineWidth = 14;
        ctx.beginPath();
        ctx.moveTo(arm_2_end_x, arm_2_end_y);
        ctx.lineTo(arm_3_end_x, arm_3_end_y);
        ctx.stroke();

        ctx.strokeStyle = "#157206";
        ctx.lineWidth = 100;
        ctx.beginPath();
        ctx.moveTo(canvas_width / 2, canvas_height);
        ctx.lineTo(canvas_width / 2, canvas_height / 1.30);
        ctx.stroke();

        // Draw smaller orange circles over the joints
        var smaller_radius = joint_radius * 0.7; // 30% smaller
        ctx.fillStyle = "orange";
        ctx.beginPath();
        ctx.arc(canvas_width / 2, canvas_height / 1.30, smaller_radius, 0, Math.PI * 2);
        ctx.fill();

        ctx.beginPath();
        ctx.arc(arm_end_x, arm_end_y, smaller_radius, 0, Math.PI * 2);
        ctx.fill();

        var smaller_radius = joint_radius; // 30% smaller
        ctx.fillStyle = "#FFFFFF";
        ctx.beginPath();
        ctx.arc(arm_2_end_x, arm_2_end_y, smaller_radius, 0, Math.PI * 2);
        ctx.fill();

        let art1 = parseInt(arm_angles[0]);
        let art2 = parseInt(arm_angles[1]);

    }

    function errorHandler(error) {console.error('Erro:', error);}

    function mouseDown(event) {
        click_state = 1;
        mouse_start_x = event.offsetX;
        mouse_start_y = event.offsetY;
    }

    function mouseUp() {
        click_state = 0;
        deposit_button_state = 0;
    }

    function mouseMove(event) {
        if (click_state === 1) {
            mouse_current_x = event.offsetX; mouse_current_y = event.offsetY;
            var dx = mouse_current_x - mouse_start_x; var dy = mouse_current_y - mouse_start_y;

            if (Math.abs(dx) > mouse_move_threshold || Math.abs(dy) > mouse_move_threshold) {
                arm_angles[0] -= dx * 0.5; // Invert rotation direction
                arm_angles[1] += dy * 0.5; // Adjust rotation direction

                arm_angles[0] = Math.max(20, Math.min(170, arm_angles[0])); // Limit angle of the first arm to 270 degrees
                var max_angle_2 = 135; // Limit angle of the second arm relative to the first arm
                arm_angles[1] = Math.max(0, Math.min(max_angle_2, arm_angles[1]));

                mouse_start_x = mouse_current_x; mouse_start_y = mouse_current_y;
                
                updateView();
            }
        }
    }

    function touchStart(event) {
        touch_state = 1;
        mouse_start_x = event.touches[0].pageX - canvas.getBoundingClientRect().left;
        mouse_start_y = event.touches[0].pageY - canvas.getBoundingClientRect().top;
        event.preventDefault();
    }

    function touchEnd() {
        touch_state = 0;
        deposit_button_state = 0; // Reset button state on touch end
    }

    function touchMove(event) {
        if (touch_state === 1) {
            mouse_current_x = event.touches[0].pageX - canvas.getBoundingClientRect().left;
            mouse_current_y = event.touches[0].pageY - canvas.getBoundingClientRect().top;
            var dx = mouse_current_x - mouse_start_x;
            var dy = mouse_current_y - mouse_start_y;

            if (Math.abs(dx) > mouse_move_threshold || Math.abs(dy) > mouse_move_threshold) {
                arm_angles[0] -= dx * 0.5; // Invert rotation direction
                arm_angles[1] += dy * 0.5; // Adjust rotation direction

                // Limit angle of the first arm to 270 degrees
                arm_angles[0] = Math.max(20, Math.min(170, arm_angles[0]));

                // Limit angle of the second arm relative to the first arm
                var max_angle_2 = 135;
                arm_angles[1] = Math.max(0, Math.min(max_angle_2, arm_angles[1]));

                mouse_start_x = mouse_current_x; mouse_start_y = mouse_current_y;

                updateView();
            }
        }
        event.preventDefault();
    }

    // Toggle deposit button state and update view
    function toggleDepositButtonState() {
        deposit_button_state = deposit_button_state ? 0 : 1; // Toggle button state
        updateView();
        depositButtonValue.textContent = deposit_button_state;
    }

    
</script>


<body onload="init()">

    <h2>Braço - Navegação Inversa</h2>

    <div class="ca57">
        <div class="limite">
            <?php include_once('webcam.php'); ?>
        </div>
    </div>

    <div class="ca75">

        <div id="braco"><canvas id="canvas"></canvas></div>
        
        <div class="slider-container c">
            <div class="base-text">Base: <span id="baseValue">0</span>º</div>
            <input type="range" min="0" max="180" value="0" class="slider" id="base_Slider" oninput="servo_base()">
        </div>

        <div class="c">

        <div class="switch-container">
            <label class="switch">
                <input type="checkbox" id="electromagnetSwitch" onchange="eletro()">
                <span class="slider-round"></span>
            </label>
            <div class="button-text">Eletroíman</div>
            
            <div class="slider-value2" id="electromagnetValue">0</div>
        </div>
        
        <div class="button-container">
            <div class="round-button" id="depositButton" ontouchstart="toggleDepositButtonState()" ontouchend="toggleDepositButtonState()"></div>
            <div class="button-text">Depositar</div>
            <div class="slider-value3" id="depositButtonValue">0</div>
        </div>
    </div>

        <div class="limpar"></div>
        <!--<p id="referencias">...</p>-->
    </div>

    <script>
        // Update button value display
        var depositButton = document.getElementById("depositButton");
        var depositButtonValue = document.getElementById("depositButtonValue");
        depositButton.addEventListener("mousedown", function () {
            depositButton.classList.add("active");
            depositButtonValue.textContent = "1";
        });

        depositButton.addEventListener("mouseup", function () {
            depositButton.classList.remove("active");
            depositButtonValue.textContent = "0";
        });
    </script>
</body>

