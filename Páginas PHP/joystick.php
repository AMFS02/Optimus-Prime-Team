<script>
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open("GET", "motor_ajax.php?q=" + 'man', true);
    xmlhttp.send()
</script>

<div>
    <!--
        <div>Valores do joystick</div>
        <div>X Coordinate: <span id="x_coordinate">0</span></div>
        <div>Y Coordinate: <span id="y_coordinate">0</span></div>
        <div>Angle: <span id="angle">0</span></div>
    -->
</div>


<div class="ca57">
     <?php include_once('webcam.php'); ?>
</div>

<div class="ca75">    
    <div class="canvas-container">
        <canvas id="canvas" name="game"; height = 375%;></canvas>
    </div>
</div>

    <script>
        var canvas, ctx;
        var directions = [];
        var width, height, radius, x_orig, y_orig;
        var coord = { x: 0, y: 0 };
        var paint = false;

        window.addEventListener('load', () => {
            canvas = document.getElementById('canvas');
            ctx = canvas.getContext('2d');
            resize();

            document.addEventListener('mousedown', startDrawing);
            document.addEventListener('mouseup', stopDrawing);
            document.addEventListener('mousemove', Draw);

            document.addEventListener('touchstart', startDrawing);
            document.addEventListener('touchend', stopDrawing);
            document.addEventListener('touchcancel', stopDrawing);
            document.addEventListener('touchmove', Draw);

            window.addEventListener('resize', resize);
        });

        function resize() {
            const canvasContainer = document.querySelector('.canvas-container');
            width = canvasContainer.clientWidth;
            height = canvasContainer.clientHeight;

            radius = Math.min(width, height) / 6; // Adjust radius based on the container size
            ctx.canvas.width = width;
            ctx.canvas.height = height;

            background();
            joystick(width / 2, height / 2); // Center the joystick in the canvas
        }

        function background() {
            x_orig = width / 2;
            y_orig = height / 2;
            ctx.beginPath();
            ctx.arc(x_orig, y_orig, radius + 20, 0, Math.PI * 2, true);
            ctx.fillStyle = '#ECE5E5';
            ctx.fill();
        }

        function joystick(width, height) {
            ctx.beginPath();
            ctx.arc(width, height, radius, 0, Math.PI * 2, true);
            ctx.fillStyle = '#0a747c';
            ctx.fill();
            ctx.strokeStyle = '#7daaad';
            ctx.lineWidth = 8;
            ctx.stroke();
        }

        function getPosition(event) {
            if (event) {
                var mouse_x = event.clientX || event.touches[0].clientX;
                var mouse_y = event.clientY || event.touches[0].clientY;
                coord.x = mouse_x - canvas.offsetLeft;
                coord.y = mouse_y - canvas.offsetTop;
            }
        }

        function is_it_in_the_circle() {
            var current_radius = Math.sqrt(Math.pow(coord.x - x_orig, 2) + Math.pow(coord.y - y_orig, 2));
            return radius >= current_radius;
        }

        function startDrawing(event) {
            paint = true;
            getPosition(event);
            if (is_it_in_the_circle()) {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                background();
                joystick(coord.x, coord.y);
                Draw();
            }
        }

        function stopDrawing() {
            paint = false;
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            background();
            joystick(width / 2, height / 2);

            // Reset displayed values

            // document.getElementById("x_coordinate").innerHTML = 0;
            // document.getElementById("y_coordinate").innerHTML = 0;
            // document.getElementById("angle").innerHTML = 0;
           
            ajaxmotores("btparar");

            if (directions.length > 0) {
                document.getElementById("downloadButton").style.display = 'block';
            }
        }

        function Draw(event) {

            if (paint) {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                background();
                var angle_in_degrees, x, y, speed;
                var angle = Math.atan2((coord.y - y_orig), (coord.x - x_orig));

                if (Math.sign(angle) == -1) {
                    angle_in_degrees = Math.round(-angle * 180 / Math.PI);
                } else {
                    angle_in_degrees = Math.round(360 - angle * 180 / Math.PI);
                }

                if (is_it_in_the_circle()) {
                    joystick(coord.x, coord.y);
                    x = coord.x;
                    y = coord.y;
                } else {
                    x = radius * Math.cos(angle) + x_orig;
                    y = radius * Math.sin(angle) + y_orig;
                    joystick(x, y);
                }

                getPosition(event);
                speed = Math.round(100 * Math.sqrt(Math.pow(x - x_orig, 2) + Math.pow(y - y_orig, 2)) / radius);

                // Update the div elements with the calculated coordinates

                // document.getElementById("x_coordinate").innerHTML = x;
                // document.getElementById("y_coordinate").innerHTML = y;
                // document.getElementById("angle").innerHTML = angle_in_degrees;

                var direction;

                if (60 <= angle_in_degrees && angle_in_degrees <= 120 && x > 0 && y > 0)  {
                    direction = "btcima";
                } else if (150 <= angle_in_degrees && angle_in_degrees <= 210 && x > 0 && y > 0) {
                    direction = "btesquerda";
                } else if (((0 <= angle_in_degrees && angle_in_degrees <= 30) || (300 <= angle_in_degrees && angle_in_degrees <= 360)) && x > 0 && y > 0) {
                    direction = "btdireita";
                } else if (240 <= angle_in_degrees && angle_in_degrees <= 300 && x > 0 && y > 0){
                    direction = "btbaixo";
                } else {
                    direction = "btparar";
                }

                ajaxmotores(direction);
            }
    }

        function ajaxmotores(direction){
        
            var xmlhttp = new XMLHttpRequest();
                xmlhttp.open("GET", "motor_ajax.php?q=" + direction, true);
                xmlhttp.send();
        }
    </script>
