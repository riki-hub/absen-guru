<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Scanner</title>
<script src="../js/html5-qrcode.min.js"></script>
</head>
<body>
    <div id="qr-reader" style="width: 500px; height: 500px;"></div>
    <div id="result"></div>

    <script>
        function onScanSuccess(decodedText, decodedResult) {
            // Capture the current time
            const now = new Date();
            const jam = now.getHours();
            const menit = now.getMinutes();

            // Process time based on your logic
            let kolom_jam;
            if (jam == 11) {
                if (menit >= 35) {
                    kolom_jam = 'jam7';
                } else if (menit >= 0 && menit < 35) {
                    kolom_jam = 'jam6';
                }
            } else if (jam == 12) {
                if (menit >= 10 && menit < 45) {
                    kolom_jam = 'istirahat2';
                } else if (menit >= 45 && menit < 60) {
                    kolom_jam = 'jam8';
                }
            } else if (jam == 13) {
                if (menit >= 20 && menit < 55) {
                    kolom_jam = 'jam9';
                } else if (menit >= 55 && menit < 60) {
                    kolom_jam = 'jam10';
                }
            } else if (jam == 10) {
                if (menit >= 25 && menit < 60) {
                    kolom_jam = 'jam5';
                }
            } else if (jam == 9) {
                if (menit >= 15 && menit < 50) {
                    kolom_jam = 'jam4';
                }
            } else if (jam == 8) {
                if (menit >= 5 && menit < 40) {
                    kolom_jam = 'jam2';
                } else if (menit >= 40 && menit < 60) {
                    kolom_jam = 'jam3';
                }
            } else if (jam == 7 && menit >= 30 && menit < 60) {
                kolom_jam = 'jam1';
            } else if (jam == 6 && menit < 60) {
                kolom_jam = 'jam0';
            }

            // Display result
            document.getElementById('result').innerHTML = `Barcode: ${decodedText}<br>Waktu: ${jam}:${menit}<br>Kolom Jam: ${kolom_jam}`;
        }

        function onScanError(errorMessage) {
            console.warn(`QR Code scan error: ${errorMessage}`);
        }

        const html5QrCode = new Html5Qrcode("qr-reader");
        html5QrCode.start(
            { facingMode: "environment" },
            {
                fps: 10,
                qrbox: 250
            },
            onScanSuccess,
            onScanError
        ).catch(err => {
            console.error(`Unable to start scanning: ${err}`);
        });
    </script>
</body>
</html>
