    <div id="container" class="l">
        <video autoplay="true" id="video" controls="true" autoplay></video>
    </div>


<script>
    let video = document.querySelector("#video");
    if(navigator.mediaDevices.getUserMedia){
        navigator.mediaDevices.getUserMedia({ video: true })
        .then(function (stream){
            video.srcObject = stream;
        })
        .catch( function(erros){
            console.log('Erro ao ligar a webcam.');
        })
    }
    else { console.log('Erro ao ligar a webcam.'); }
</script>