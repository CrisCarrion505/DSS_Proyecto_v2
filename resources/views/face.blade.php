<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro Facial</title>
    <style>
        /* Estilo general de la página */
        body {
            font-family: Arial, sans-serif;
            background-color: #e6f0fa; /* azul muy claro mate */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 100vh;
            margin: 0;
            padding: 40px 20px;
            color: #1a3e5c; /* azul oscuro */
        }

        h2 {
            color: #1a3e5c;
            margin-bottom: 30px;
        }

        /* Video con borde suave */
        video {
            border: 2px solid #4a6e8c; /* azul mate */
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.2);
        }

        /* Botones estilizados */
        button {
            background-color: #4a6e8c;
            color: #fff;
            border: none;
            padding: 12px 25px;
            margin: 15px 10px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.2s;
        }

        button:hover {
            background-color: #3b5870;
            transform: translateY(-2px);
        }

        /* Contenedor de botones */
        .buttons {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }
    </style>
    @csrf
</head>
<body>

<h2>Registro Facial</h2>

<video id="video" width="400" autoplay></video>

<div class="buttons">
    <button onclick="registrar()">Registrar rostro</button>
    <button onclick="verificar()">Verificar rostro</button>
</div>

<canvas id="canvas" width="400" height="300" style="display:none;"></canvas>

<script>
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const ctx = canvas.getContext('2d');

navigator.mediaDevices.getUserMedia({ video: true })
    .then(stream => video.srcObject = stream);

function enviar(url) {
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    canvas.toBlob(blob => {
        const formData = new FormData();
        formData.append('image', blob, 'face.jpg');
        formData.append('_token', '{{ csrf_token() }}');

        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => alert(JSON.stringify(data)));
    }, 'image/jpeg');
}

function registrar() {
    enviar('/face/register');
}

function verificar() {
    enviar('/face/verify');
}
</script>

</body>
</html>
