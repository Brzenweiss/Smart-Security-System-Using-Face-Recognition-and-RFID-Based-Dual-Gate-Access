<?php 

require 'function.php';

if ( isset($_POST["register"]) ) {

    if( registrasi($_POST) > 0 ) {
        echo "<script>
                alert('user baru berhasil ditambahakan!')
              </script>";
    } else {
        echo mysqli_error($koneksi);
        }
}


?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Halaman Registrasi</title>
    <link rel="stylesheet" href="css/style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Borel&family=Merriweather:wght@300;400;700&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet" />
  </head>
  <body>
    <div class="bg-img vh-100">
      <!-- NAVBAR -->
      <nav class="navbar">
        <div class="container-fluid">
          <span style="color: white" class="text-style navbar-text text-center mx-auto fs-1"> Parkir Universitas Tidar </span>
        </div>
      </nav>
      <!-- NAVBAR -->

      <!-- Logo -->
      <section class="text-style content-header ms-5 mt-5">
        <h1><img src="../img/logo.png" width="45px" height="45px" /> Halaman Registrasi</h1>
      </section>

      <!-- FORM -->
      <div class="border border-primary rounded-3 bg-light bg-gradient mx-auto mt-5 p-3 ms-3 me-3">
        <form action="" method="post">
          <div class="mb-3">
            <label for="username" class="text-style form-label">Username</label>
            <input type="text" class="form-control border border-primary" name="username" id="username" aria-describedby="emailHelp" />
          </div>
          <div class="mb-3">
            <label for="password" class="text-style form-label">Password</label>
            <input type="password" class="form-control border border-primary" name="password" id="password" />
          </div>
          <div class="mb-3">
            <label for="password2" class="text-style form-label">Konfirmasi Password</label>
            <input type="password" class="form-control border border-primary" name="password2" id="password2" />
          </div>
          <div><a href="login.php" class="text-style text-decoration-none">Sudah punya akun?</a></div>
          <button type="submit" name="register" class="btn btn-primary mt-3" >Register</button>
          <a href="login.php"><button type="button" class="btn btn-danger mt-3 ms-3" >Cancel</button></a>
        </form>
      </div>
    </div>
    <!-- FORM -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="js/modal.js"></script>
  </body>
</html>