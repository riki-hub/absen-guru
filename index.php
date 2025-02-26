<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.4.1/css/simple-line-icons.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


</head>

<style>
  body {
    background-color: #dee9ff;
  }

  .registration-form {
    padding: 50px 0;
  }

  .registration-form form {
    background-color: #fff;
    max-width: 600px;
    margin: auto;
    padding: 50px 70px;
    border-top-left-radius: 30px;
    border-top-right-radius: 30px;
    box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.075);
  }

  .registration-form .form-icon {
    text-align: center;
    background-color: #5891ff;
    border-radius: 50%;
    font-size: 40px;
    color: white;
    width: 100px;
    height: 100px;
    margin: auto;
    margin-bottom: 50px;
    line-height: 100px;
  }

  .registration-form .item {
    border-radius: 20px;
    margin-bottom: 25px;
    padding: 10px 20px;
  }

  .registration-form .create-account {
    border-radius: 30px;
    padding: 10px 20px;
    font-size: 18px;
    font-weight: bold;
    background-color: #5791ff;
    border: none;
    color: white;
    margin-top: 20px;
  }

  .registration-form .social-media {
    max-width: 600px;
    background-color: #fff;
    margin: auto;
    padding: 35px 0;
    text-align: center;
    border-bottom-left-radius: 30px;
    border-bottom-right-radius: 30px;
    color: #9fadca;
    border-top: 1px solid #dee9ff;
    box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.075);
  }

  .registration-form .social-icons {
    margin-top: 30px;
    margin-bottom: 16px;
  }

  .registration-form .social-icons a {
    font-size: 23px;
    margin: 0 3px;
    color: #5691ff;
    border: 1px solid;
    border-radius: 50%;
    width: 45px;
    display: inline-block;
    height: 45px;
    text-align: center;
    background-color: #fff;
    line-height: 45px;
  }

  .registration-form .social-icons a:hover {
    text-decoration: none;
    opacity: 0.6;
  }

  @media (max-width: 576px) {
    .registration-form form {
      padding: 50px 20px;
    }

    .registration-form .form-icon {
      width: 70px;
      height: 70px;
      font-size: 30px;
      line-height: 70px;
    }
  }
</style>

<body>
  <div class="registration-form">
    <form method="post" action="cek.php">
      <center>
        <img style="margin-bottom: 20px;" src="images/logo-removebg-preview.png" width="200px" height="150px" alt="" srcset="">
      </center>
      <div class="form-group">
        <input type="text" class="form-control item" id="username" placeholder="Username" name="username" />
      </div>
      <div class="form-group">
        <input type="password" class="form-control item" id="password" name="password" placeholder="Password" />
      </div>

      <input type="hidden" class="form-control item" id="latitude" placeholder="Username" name="latitude" />


      <input type="hidden" class="form-control item" id="longitude" placeholder="Username" name="longitude" />



      <div class="form-group">
        <button type="submit" name="cek" class="btn btn-block create-account">
          LOGIN
        </button>
      </div>
    </form>
    <div class="social-media">
      <h6>Lupa Password? Hubungi Tim RPL.</h6>
    </div>
  </div>
  <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
  <script src="assets/js/script.js"></script>
</body>

<script>
  // Get location automatically when page loads
  window.onload = getLocation();

  function requestLocation() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(showPosition, showError);
    } else {
      alert("Geolocation is not supported by this browser.");
    }
  }

  function showPosition(position) {
    // Send location data to the server or process it as needed
    console.log("Latitude: " + position.coords.latitude +
      " Longitude: " + position.coords.longitude);
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


  function showError(error) {
    switch (error.code) {
      case error.PERMISSION_DENIED:
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: "Pengguna menolak Permintaan Lokasi!",
          confirmButtonText: "OK",
          allowOutsideClick: false,
          preConfirm: () => {
            window.location.href = "index.php";
          }
        });
        break;
      case error.POSITION_UNAVAILABLE:
        alert("Location information is unavailable.");
        break;
      case error.TIMEOUT:
        alert("The request to get user location timed out.");
        break;
      case error.UNKNOWN_ERROR:
        alert("An unknown error occurred.");
        break;
    }
  }

  // Request location on page load
  window.onload = requestLocation;
</script>
<?php
if (isset($_GET['error']) && $_GET['error'] == 1) {
  echo '<script>
            Swal.fire({
                icon: "error",
                title: "Login Gagal",
                text: "Username atau password salah. Silakan coba lagi.",
                confirmButtonText: "OK",
                preConfirm: () => {
                  // Redirect to another page when "OK" is clicked
                  window.location.href = "index.php"; // Ganti dengan file yang diinginkan
                }
            });
        </script>';
}

if (isset($_GET['error']) && $_GET['error'] == 2) {
  echo '<script>
            Swal.fire({
                icon: "error",
                title: "Login Gagal",
                text: "Pastikan anda berada di SMK MADYA DEPOK",
                confirmButtonText: "OK",
                preConfirm: () => {
                  // Redirect to another page when "OK" is clicked
                  window.location.href = "index.php"; // Ganti dengan file yang diinginkan
                }
            });
        </script>';
}
?>

</html>