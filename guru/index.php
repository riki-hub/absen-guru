<?php
include '../koneksi.php';
session_start();
$_SESSION['gambar'];
$_SESSION['user_id'];
if (!isset($_SESSION['nama'])) {
  header("Location: ../");
  exit();
}


if (!isset($_SESSION['role']) || $_SESSION['role'] != 'guru') {
  echo "<script>
      alert('Anda tidak bisa mengakses halaman ini karena bukan guru');
      window.history.back(); // Mengarahkan kembali ke halaman sebelumnya
  </script>";
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard | Guru</title>
  <link rel="shortcut icon" href="../images/favicon.png" />
  <script src="../js/html5-qrcode.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f0f0f0;
    }

    .container {
      padding: 20px;
    }

    index-btn {
      display: inline-block;
      padding: 10px 20px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .overlay {
      display: none;
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.8);
      z-index: 1000;
      justify-content: center;
      align-items: flex-end;
      animation: slideUp 0.5s ease-out forwards;
    }

    .overlay.active {
      display: flex;
    }

    @keyframes slideUp {
      from {
        transform: translateY(100%);
      }

      to {
        transform: translateY(0);
      }
    }

    .scan-container {
      background-color: white;
      padding: 20px;
      border-radius: 10px 10px 0 0;
      text-align: center;
      width: 100%;
      max-width: 600px;

      /* Menggunakan vh untuk responsivitas tinggi layar */
      position: relative;
      box-sizing: border-box;
      overflow: auto;
    }


    .close-btn {
      position: absolute;
      top: 10px;
      right: 10px;
      background: none;
      border: none;
      font-size: 20px;
      cursor: pointer;
      color: #333;
    }

    @media (min-height: 800px) {
      .scan-container {
        max-height: 50vh;
        height: 80vh;
      }
    }

    @media (max-width: 600px) {
      .scan-container {
        padding: 10px;
        max-width: 80vh;
        height: 80vh;
        /* Menambah tinggi container pada layar kecil */
      }

      .close-btn {
        top: 5px;
        right: 5px;
        font-size: 18px;
      }

      #reader {
        width: 100%;
        height: 100%;
        /* Meningkatkan tinggi kamera pada layar kecil */
      }
    }
  </style>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
      height: 100vh;
    }

    .header {
      background: linear-gradient(135deg, #6a11cb, #2575fc);
      padding: 20px;
      text-align: center;
      color: #fff;
    }

    .header img {
      border-radius: 50%;
      width: 60px;
      border: 2px solid #fff;
    }

    .header h1 {
      margin: 10px 0 5px;
      font-size: 24px;
    }

    .header p {
      margin: 0;
      font-size: 14px;
    }

    .logout-button {
      position: absolute;
      top: 20px;
      right: 20px;
      background-color: #dc3545;
      /* Warna tombol logout */
      color: #fff;
      padding: 10px 15px;
      text-decoration: none;
      border-radius: 5px;
      font-size: 14px;
    }


    .logout-button:hover {
      background-color: #c82333;
      text-decoration: none;
      /* Warna saat hover */
    }

    .content {
      flex: 1;
      padding: 20px;
      overflow-y: auto;
    }

    .project {
      background: #fff;
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 15px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .project p {
      margin: 0;
      font-size: 18px;
      font-weight: bold;
      color: #333;
    }

    .attendance {
      display: flex;
      justify-content: space-between;
      margin-top: 15px;
    }

    .attendance div {
      background: #fff;
      padding: 10px;
      border-radius: 10px;
      text-align: center;
      width: 45%;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .attendance2 div {
      background: #fff;
      padding: 10px;
      border-radius: 10px;
      text-align: center;
      margin-top: 30px;
      height: 30px;
      width: 96%;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .attendance div p {
      margin: 5px 0;
      font-size: 16px;
    }

    .attendance div:first-child {
      color: #4caf50;
    }

    .attendance div:last-child {
      color: #f44336;
    }

    .stats {
      background: #fff;
      padding: 15px;
      border-radius: 10px;
      margin-top: 15px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .stats p {
      margin: 5px 0;
      font-size: 16px;
      color: #333;
    }

    .stats .percentage {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .history {
      background: #fff;
      padding: 15px;
      border-radius: 10px;
      margin-top: 15px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .history p {
      margin: 0;
      font-size: 16px;
      color: #333;
    }

    .footer {
      display: flex;
      justify-content: space-around;
      position: relative;
      bottom: 0;
      width: 100%;
      background: #fff;
      padding: 10px 0;
      box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
    }

    .footer div {
      text-align: center;
      flex: 1;
    }

    .footer div i {
      font-size: 24px;
      color: #6a11cb;
    }

    .footer div p {
      margin: 0;
      font-size: 12px;
    }

    .jarak {
      margin: 5px;
    }

    .footer div.active i {
      color: #2575fc;
    }

    .floating-btn {
      position: absolute;
      top: -30px;
      left: 50%;
      transform: translateX(-50%);
      background: linear-gradient(135deg, #6a11cb, #2575fc);
      color: #fff;
      border: none;
      border-radius: 50%;
      width: 60px;
      height: 60px;
      display: flex;
      justify-content: center;
      align-items: center;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
      cursor: pointer;
      z-index: 1;
    }

    .break-time {
      width: 100%;
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      text-align: center;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      font-size: 16px;
      color: #333;
      margin-top: 15px;
    }
    .logout-button:hover {
      background-color: #fff;
      color : red ;
    }
    .profile p {
      color: black ;
      text-decoration: none;

    }
    .profile p:hover {
     color : white;
    }
  </style>
</head>


<body onload="updateAttendance()">

  <div class="header">

    <img src="../upload/<?php echo $_SESSION['gambar'];  ?>" alt="Profile Picture" />
    <h1><?php echo $_SESSION['nama']; ?></h1>
    <a href="logot.php" class="logout-button">
      <i class="fas fa-sign-out-alt"></i> Logout
    </a>
  </div>
  <div class="content">
    <div class="project">
      <p id="dayDisplay"></p>
      <div class="attendance">
        <div>
          <p><i class="fas fa-sign-in-alt"></i> Masuk</p>
          <p id="masuk"></p>
        </div>
        <span class="jarak"></span>
        <div>
          <p><i class="fas fa-sign-out-alt"></i> Selesai</p>
          <p id="selesai"></p>
        </div>
      </div>
      <div class="attendance2" id="breakTime" style="display: none;">
        <div>
          <p>Istirahat</p>
        </div>
      </div>
    </div>
    <div class="history">
      <div class="d-flex flex-row gap-3">
        <div class="">
          <a style="text-decoration: none;" href="tambahan.php?id=<?= $_SESSION['user_id'] ?>">
            <div class="card text-center" style="width: 120px;">
              <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <i class="fa fa-receipt fa-4x" style="color: #b8d9ff;"></i>
                <h5 class="card-title mt-3">Riwayat</h5>
              </div>
            </div>
          </a>
        </div>
        <span style="color:transparent">ao</span>
      </div>
    </div>


  </div>




  </div>
  <div class="footer">
    <div>
      <i class="fas fa-home"></i>
      <p>Home</p>
    </div>
    <button id="startScanBtn" class="floating-btn">
      <i class=" fas fa-camera"></i>
    </button>

    <div class="profile">
      <a href="profile.php">
        <i class="fas fa-user"></i>
        <p>Profile</p>
      </a>
    </div>

  </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="modal fade" id="scanModal" tabindex="-1" aria-labelledby="scanModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="scanModalLabel">Scan QR Code</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="reader" style="width: 100%;"></div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="scanResultModal" tabindex="-1" aria-labelledby="scanResultModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="scanResultModalLabel">Scan Result</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="result" id="scanResultText">
        <form action="proses/scan/masuk.php" method="post" id="locationForm">
          <div class="row">
            <div class="col-md-5 mb-3">
              <label for="">Nama Guru</label>
              <input class="form-control" type="text" name="nama_guru" value="<?= $_SESSION['nama']; ?>" id="" readonly>
            </div>
            <div class="col-md-3 ">
              <label for="">Kelas</label>
              <input class="form-control" type="text" name="kelas" id="kelas" readonly>
            </div>
            <div class="col-md-3 ">
              <label for="">Jam</label>
              <input class="form-control" type="text" name="jamcuy" id="jam" readonly>
            </div>
            <div class="col-md-4 mb-3 ">
              <label for="">Tanggal</label>
              <input class="form-control" type="text" name="beda" id="tanggal" readonly>
            </div>
          </div>


          <video id="video" autoplay style="display:none; width: 100%; height: auto;"></video>
          <canvas id="canvas" style="display:none; width: 100%; height: auto;"></canvas>

          <input class="btn btn-primary" type="hidden" id="snap" style="display:none;"></input>
          <input type="hidden" name="photo" id="photo">
      </div>
      <div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
  <button type="submit" class="btn btn-primary" onclick="takePicture()">Masuk</button>
</div>
      </form>
    </div>
  </div>
</div>

<script>
  function updateAttendance() {
    const currentDate = new Date();
    const days = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
    const currentDay = days[currentDate.getDay()];
    document.getElementById("dayDisplay").innerHTML = `${currentDay}, <span id="jamKe"></span>`;

    const currentHour = currentDate.getHours();
    const currentMinute = currentDate.getMinutes();
    let jamAbsen, masukTime, selesaiTime;
    let isBreakTime = false;

    // Define time slots for each day
    const schedule = {
      "Senin": [{
          start: [6, 0],
          end: [7, 0],
          jam: 0
        },
        {
          start: [8, 0],
          end: [8, 35],
          jam: 1
        },
        {
          start: [8, 35],
          end: [9, 10],
          jam: 2
        },
        {
          start: [9, 10],
          end: [9, 45],
          jam: 3
        },
        {
          start: [9, 45],
          end: [10, 20],
          jam: 4
        },
        {
          start: [10, 20],
          end: [10, 50],
          jam: "Istirahat 1",
          break: true
        },
        {
          start: [10, 50],
          end: [11, 25],
          jam: 5
        },
        {
          start: [11, 25],
          end: [12, 0],
          jam: 6
        },
        {
          start: [12, 0],
          end: [12, 30],
          jam: "Istirahat 2",
          break: true
        },
        {
          start: [12, 30],
          end: [13, 5],
          jam: 7
        },
        {
          start: [13, 5],
          end: [13, 40],
          jam: 8
        },
        {
          start: [13, 40],
          end: [14, 15],
          jam: 9
        },
        {
          start: [14, 15],
          end: [14, 50],
          jam: 10
        }
      ],
      "Jumat": [

        {
          start: [7, 45],
          end: [8, 20],
          jam: 1
        },
        {
          start: [8, 20],
          end: [8, 55],
          jam: 2
        },
        {
          start: [8, 55],
          end: [9, 30],
          jam: 3
        },
        {
          start: [9, 30],
          end: [9, 50],
          jam: "Istirahat",
          break: true
        },
        {
          start: [9, 50],
          end: [10, 25],
          jam: 4
        },
        {
          start: [10, 25],
          end: [11, 0],
          jam: 5
        },
        {
          start: [11, 0],
          end: [11, 35],
          jam: 6
        }
      ],
      "default": [{
          start: [7, 30],
          end: [8, 5],
          jam: 1
        },
        {
          start: [8, 5],
          end: [8, 40],
          jam: 2
        },
        {
          start: [8, 40],
          end: [9, 15],
          jam: 3
        },
        {
          start: [9, 15],
          end: [9, 50],
          jam: 4
        },
        {
          start: [9, 50],
          end: [10, 25],
          jam: "Istirahat 1",
          break: true
        },
        {
          start: [10, 25],
          end: [11, 0],
          jam: 5
        },
        {
          start: [11, 0],
          end: [11, 35],
          jam: 6
        },
        {
          start: [11, 35],
          end: [12, 10],
          jam: 7
        },
        {
          start: [12, 10],
          end: [12, 45],
          jam: "Istirahat 2",
          break: true
        },
        {
          start: [12, 45],
          end: [13, 20],
          jam: 8
        },
        {
          start: [13, 20],
          end: [13, 55],
          jam: 9
        },
        {
          start: [13, 55],
          end: [14, 30],
          jam: 10
        }
      ]
    };


    // Get the correct schedule for the current day
    const todaySchedule = schedule[currentDay] || schedule["default"];

    // Find the current slot
    for (let slot of todaySchedule) {
      const [startHour, startMinute] = slot.start;
      const [endHour, endMinute] = slot.end;

      if ((currentHour > startHour || (currentHour === startHour && currentMinute >= startMinute)) &&
        (currentHour < endHour || (currentHour === endHour && currentMinute < endMinute))) {
        jamAbsen = slot.jam;
        masukTime = `${String(startHour).padStart(2, '0')}:${String(startMinute).padStart(2, '0')}`;
        selesaiTime = `${String(endHour).padStart(2, '0')}:${String(endMinute).padStart(2, '0')}`;
        isBreakTime = slot.break || false;
        break;
      }
    }

    if (jamAbsen == null) {
      masukTime = "--:--";
      selesaiTime = "--:--";
      jamAbsen = "(Diluar jam absen)";
    }

    document.getElementById("jamKe").innerText = isBreakTime ? "istirahat" : (jamAbsen !== null ? `Jam Ke-${jamAbsen}` : "--");
    if (isBreakTime) {
      document.getElementById("masuk").innerText = "";
      document.getElementById("selesai").innerText = "";
      document.getElementById("breakTime").style.display = "block";
    } else {
      document.getElementById("masuk").innerText = masukTime;
      document.getElementById("selesai").innerText = selesaiTime;
      document.getElementById("breakTime").style.display = "none";
    }
  }

  setInterval(updateAttendance, 100); // Memperbarui setiap 60 detik (1 menit)
  updateAttendance(); // Panggil fungsi langsung setelah interval dimulai
</script>

<script>
  const video = document.getElementById('video');
  const canvas = document.getElementById('canvas');
  const context = canvas.getContext('2d');
  const snapButton = document.getElementById('snap');
  const photoInput = document.getElementById('photo');

  document.getElementById("")

  function startCamera() {
    navigator.mediaDevices.getUserMedia({
        video: true
      })
      .then((stream) => {
        video.srcObject = stream;
        video.style.display = 'block';
        snapButton.style.display = 'inline';

        // Update canvas size to match video size
        video.onloadedmetadata = () => {
          canvas.width = video.videoWidth;
          canvas.height = video.videoHeight;
        };
      })
      .catch((err) => {
        console.error("Error accessing camera: " + err);
      });
  }

  function takePicture() {
    // Use dynamic size based on the actual video size
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    const dataURL = canvas.toDataURL('image/png');
    photoInput.value = dataURL; // Save the image as a base64 string
    video.style.display = 'none';
    canvas.style.display = 'block';
    stopCamera();
  }

  function stopCamera() {
    let stream = video.srcObject;
    let tracks = stream.getTracks();
    tracks.forEach(track => track.stop());
    video.srcObject = null;
  }

  function getLocation() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition((position) => {
        document.getElementById("latitude").value = position.coords.latitude;
        document.getElementById("longitude").value = position.coords.longitude;
      });
    } else {
      alert("Geolocation tidak didukung oleh browser ini.");
    }
  }

  // Get location automatically when page loads
  window.onload = getLocation;
</script>

<script>
  let html5QrCode;

  function onScanSuccess(decodedText, decodedResult) {
    startCamera();
    
    console.log(`Code matched = ${decodedText}`, decodedResult);
    const currentDate = new Date();
    const currentDay = currentDate.getDay();
    const currentHour = currentDate.getHours();
    const currentMinute = currentDate.getMinutes();
    const timestamp = currentDate.toLocaleTimeString();
    if (currentDay === 0 || currentDay === 6) {
      $('#scanModal').modal('hide'); // 0 = Minggu, 6 = Sabtu
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "Absen tidak dapat dilakukan ",
        confirmButtonText: "OK",
        preConfirm: () => {
          window.location.href = "index.php";
        }
      });
      return;
    }
    <?php
    include '../koneksi.php';
    $result = $koneksi->query("SELECT nama_kelas FROM kelas");

    $kelasArray = array();
    while ($row = $result->fetch_assoc()) {
      $kelasArray[] = $row['nama_kelas'];
    }
    ?>

    var kelasData = <?php echo json_encode($kelasArray); ?>;

    if (kelasData.includes(decodedText)) {
      let jamAbsen = null;
      document.getElementById('kelas').value = decodedText;

      if (currentDay === 1) { // Senin
        if (currentHour === 8 && currentMinute >= 0 && currentMinute < 30) {
          jamAbsen = 1; // 08:00 - 08:35
        } else if ((currentHour === 8 && currentMinute >= 35) || (currentHour === 9 && currentMinute < 5)) {
          jamAbsen = 2; // 08:35 - 09:10
        } else if (currentHour === 9 && currentMinute >= 10 && currentMinute < 40) {
          jamAbsen = 3; // 09:10 - 09:45
        } else if ((currentHour === 9 && currentMinute >= 45) || (currentHour === 10 && currentMinute < 15)) {
          jamAbsen = 4; // 09:45 - 10:20
        } else if (currentHour === 10 && currentMinute >= 20 && currentMinute < 50) {
          $('#scanModal').modal('hide');
          jamAbsen = "Istirahat 1"; // 10:20 - 10:50 Istirahat 1
          Swal.fire({
            title: 'Waktu Istirahat',
            text: 'Sekarang waktu istirahat 1!',
            icon: 'info',
            confirmButtonText: 'OK',
            preConfirm: () => {
              window.location.href = "index.php";
            }
          });
        } else if ((currentHour === 10 && currentMinute >= 50) || (currentHour === 11 && currentMinute < 20)) {
          jamAbsen = 5; // 10:50 - 11:25
        } else if (currentHour === 11 && currentMinute >= 25 && currentMinute < 55) {
          jamAbsen = 6; // 11:25 - 12:00
        } else if (currentHour === 12 && currentMinute >= 0 && currentMinute < 30) {
          $('#scanModal').modal('hide');
          jamAbsen = "Istirahat 2"; // 12:00 - 12:30 Istirahat 2
          Swal.fire({
            title: 'Waktu Istirahat',
            text: 'Sekarang waktu istirahat 2!',
            icon: 'info',
            confirmButtonText: 'OK',
            preConfirm: () => {
              window.location.href = "index.php";
            }
          });
        } else if (currentHour === 12 && currentMinute >= 30 && currentMinute < 60) {
          jamAbsen = 7; // 12:30 - 13:05
        } else if (currentHour === 13 && currentMinute >= 5 && currentMinute < 35) {
          jamAbsen = 8; // 13:05 - 13:40
        } else if ((currentHour === 13 && currentMinute >= 40) || (currentHour === 14 && currentMinute < 10)) {
          jamAbsen = 9; // 13:40 - 14:15
        } else if (currentHour === 14 && currentMinute >= 15 && currentMinute < 45) {
          jamAbsen = 10; // 14:15 - 14:50
        }
      } else if (currentDay === 5) { // Jumat
        if ((currentHour === 7 && currentMinute >= 45) || (currentHour === 8 && currentMinute < 15)) {
          jamAbsen = 1; // 07:45 - 08:19
        } else if ((currentHour === 8 && currentMinute >= 20) && (currentHour === 8 && currentMinute < 50)) {
          jamAbsen = 2; // 08:20 - 08:54
        } else if ((currentHour === 8 && currentMinute >= 55) || (currentHour === 9 && currentMinute < 25)) {
          jamAbsen = 3; // 08:55 - 09:29
        } else if ((currentHour === 9 && currentMinute >= 30) && (currentHour === 9 && currentMinute < 45)) {
          $('#scanModal').modal('hide'); // Menyembunyikan modal
          jamAbsen = "Istirahat"; // Waktu istirahat
          Swal.fire({
            title: 'Waktu Istirahat',
            text: 'Sekarang waktu istirahat!',
            icon: 'info',
            confirmButtonText: 'OK',
            preConfirm: () => {
              window.location.href = "index.php";
            }
          });
        } else if ((currentHour === 9 && currentMinute >= 50) || (currentHour === 10 && currentMinute < 20)) {
          jamAbsen = 4; // 09:50 - 10:24
        } else if (currentHour === 10 && currentMinute >= 25 && currentMinute < 55) {
          jamAbsen = 5; // 10:25 - 11:00
        } else if ((currentHour === 11 && currentMinute >= 0) && (currentHour === 11 && currentMinute < 30)) {
          jamAbsen = 6; // 11:00 - 11:35
        }


      } else { // Selasa sampai Kamis
        const currentTimeInMinutes = currentHour * 60 + currentMinute;

        if (currentTimeInMinutes >= 450 && currentTimeInMinutes < 480) { // 07:30 - 08:05
          jamAbsen = 1;
        } else if (currentTimeInMinutes >= 485 && currentTimeInMinutes < 515) { // 08:05 - 08:40
          jamAbsen = 2;
        } else if (currentTimeInMinutes >= 520 && currentTimeInMinutes < 550) { // 08:40 - 09:15
          jamAbsen = 3;
        } else if (currentTimeInMinutes >= 555 && currentTimeInMinutes < 585) { // 09:15 - 09:50
          jamAbsen = 4;
        } else if (currentTimeInMinutes >= 590 && currentTimeInMinutes < 620) { // 09:50 - 10:25
          $('#scanModal').modal('hide');
          jamAbsen = "Istirahat 1";
          Swal.fire({
            title: 'Waktu Istirahat 1',
            text: 'Sekarang waktu istirahat!',
            icon: 'info',
            confirmButtonText: 'OK',
            preConfirm: () => {
              window.location.href = "index.php";
            }
          });
        } else if (currentTimeInMinutes >= 625 && currentTimeInMinutes < 655) { // 10:25 - 11:00
          jamAbsen = 5;
        } else if (currentTimeInMinutes >= 660 && currentTimeInMinutes < 690) { // 11:00 - 11:35
          jamAbsen = 6;
        } else if (currentTimeInMinutes >= 695 && currentTimeInMinutes < 725) { // 11:35 - 12:10
          jamAbsen = 7;
        } else if (currentTimeInMinutes >= 730 && currentTimeInMinutes < 760) { // 12:10 - 12:45
          $('#scanModal').modal('hide');
          jamAbsen = "Istirahat 2";
          Swal.fire({
            title: 'Waktu Istirahat 2',
            text: 'Sekarang waktu istirahat dan sholat dzuhur!',
            icon: 'info',
            confirmButtonText: 'OK',
            preConfirm: () => {
              window.location.href = "index.php";
            }
          });
        } else if (currentTimeInMinutes >= 765 && currentTimeInMinutes < 795) { // 12:45 - 13:20
          jamAbsen = 8;
        } else if (currentTimeInMinutes >= 800 && currentTimeInMinutes < 830) { // 13:20 - 13:55
          jamAbsen = 9;
        } else if (currentTimeInMinutes >= 835 && currentTimeInMinutes < 865) { // 13:55 - 14:30
          jamAbsen = 10;
        }


      }
      if (jamAbsen !== null) {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0'); // Month is zero-based, so +1
        const day = String(today.getDate()).padStart(2, '0'); // Pad single digits with leading zero
        document.getElementById('tanggal').value = `${year}-${month}-${day}`;
        document.getElementById('jam').value = `${timestamp}`;
        document.getElementById('scanResultModalLabel').textContent = `Jam Absensi: Jam ke-${jamAbsen}`;
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(showPosition, showError);
        } else {
          document.getElementById("status").textContent = "Geolocation tidak didukung oleh browser ini.";
        }

        function showPosition(position) {
          const lat = position.coords.latitude;
          const lng = position.coords.longitude;
          document.getElementById('latitude').value = lat;
          document.getElementById('longitude').value = lng;
        }

        function showError(error) {
          switch (error.code) {
            case error.PERMISSION_DENIED:
              $('#scanModal').modal('hide');
              Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Pengguna menolak Permintaan Lokasi!",
                confirmButtonText: "OK",
                preConfirm: () => {
                  window.location.href = "index.php";
                }
              });
              break;
            case error.POSITION_UNAVAILABLE:
              $('#scanModal').modal('hide');
              Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Informasi Lokasi Tidak Tersedia",
                confirmButtonText: "OK",
                preConfirm: () => {
                  window.location.href = "index.php";
                }
              });
              break;
            case error.TIMEOUT:
              $('#scanModal').modal('hide');
              Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Waktu Habis",
                confirmButtonText: "OK",
                preConfirm: () => {
                  window.location.href = "index.php";
                }
              });
              break;
            case error.UNKNOWN_ERROR:
              $('#scanModal').modal('hide');
              Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Error yang tidak diketahui",
                confirmButtonText: "OK",
                preConfirm: () => {
                  window.location.href = "index.php";
                }
              });
              break;
          }
        }
        $('#scanResultModal').modal('show');
        
        $('#scanModal').modal('hide');
        
      } else {
        $('#scanModal').modal('hide');
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: "Diluar Jam Absen!",
          confirmButtonText: "OK",
          preConfirm: () => {
            window.location.href = "index.php";
          }
        });
      }
    } else {
      $('#scanModal').modal('hide');
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "Kamu salah scan Barcode!",
        confirmButtonText: "OK",
        preConfirm: () => {
          window.location.href = "index.php";
        }
      });
    }
  }


  function onScanFailure(error) {
    // Do nothing, avoid console spam
  }

  document.getElementById('startScanBtn').addEventListener('click', () => {
    $('#scanModal').modal('show');
   
   
    html5QrCode = new Html5Qrcode("reader");
    html5QrCode.start({
        facingMode: "environment"
      }, {
        fps: 10, // Sets the framerate to 10 scans per second
        qrbox: {
          width: 250,
          height: 250
        }
      },
      onScanSuccess,
      onScanFailure
    ).catch((err) => {
      console.error("Failed to start QR code scanning.", err);
    });
  });

  $('#scanModal').on('hidden.bs.modal', () => {
    if (html5QrCode) {
      html5QrCode.stop().catch((err) => {
        console.error("Failed to stop QR code scanning.", err);
      });
    }
  });
</script>


</html>