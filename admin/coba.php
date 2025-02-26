<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Generator</title>
</head>

<body>
    <div id="qrcode"></div>

    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
    <script>
        var qrcode = new QRCode(document.getElementById("qrcode"), {
            text: "https://example", // Ganti dengan teks atau URL yang ingin kamu masukkan ke QR code
            width: 128,
            height: 128,
            colorDark: "#000000", // Warna QR code
            colorLight: "#ffffff", // Warna background
            correctLevel: QRCode.CorrectLevel.H // Tingkat koreksi kesalahan (L, M, Q, H)
        });
    </script>
</body>

</html>