<?php
session_start();
require 'function.php';


// cek cookie
if (isset($_COOKIE['id']) && isset($_COOKIE['key'])) {
  $id = $_COOKIE['id'];
  $key = $_COOKIE['key'];

  // ambil username berdasarkan id
  $result = mysqli_query($koneksi, "SELECT username FROM user WHERE id = $id");
  $row = mysqli_fetch_assoc($result);

  // cek cookie dan username
  if ($key === hash('sha256', $row['username'])) {
    $_SESSION['login'] = true;
  }
}


if (isset($_SESSION["login"])) {
  header("Location: index.php");
  exit;
}


if (isset($_POST["login"])) {

  $username = $_POST["username"];
  $password = $_POST["password"];

  $result = mysqli_query($koneksi, "SELECT * FROM user WHERE username = '$username'");

  // cek username
  if (mysqli_num_rows($result) === 1) {

    // cek password
    $row = mysqli_fetch_assoc($result);
    if (password_verify($password, $row["password"])) {
      // set session
      $_SESSION["login"] = true;

      //cek remember me
      if (isset($_POST['remember'])) {
        // buat cookie
        setcookie('id', $row['id'], time() + 60);
        setcookie('key', hash('sha256', $row['username']), time() + 60);
      }

      header("Location: index.php");
      exit;
    }
  }

  $error = true;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Smart Security</title>
  <link rel="stylesheet" href="css/style.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Borel&family=Merriweather:wght@300;400;700&family=Poppins:wght@300;400;600;700&display=swap"
    rel="stylesheet" />
</head>

<body>
  <div class="bg-img vh-100">
    <!-- NAVBAR -->
    <nav class="navbar">
      <div class="container-fluid">
        <span style="color: white" class="text-style navbar-text text-center mx-auto fs-1"> Smart Security </span>
      </div>
    </nav>
    <!-- NAVBAR -->

    <!-- Logo -->
    <section class="text-style content-header mt-5 text-center">
      <img src="img/logo.png" width="100px" height="100px" alt="Logo" />
      <h1>Selamat Datang</h1>
    </section>

    <!-- FORM -->

    <div class="border border-primary rounded-3 bg-light bg-gradient mx-auto mt-5 p-3 ms-3 me-3">

      <!-- ALERT -->
      <?php if (isset($error)): ?>
        <div class="alert alert-danger text-center" role="alert">
          Username atau Password salah
        </div>
      <?php endif; ?>
      <!-- ALERT -->

      <form action="" method="post">
        <div class="mb-3">
          <label for="username" class="text-style form-label">Username</label>
          <input type="text" class="form-control border border-primary" name="username" id="username"
            aria-describedby="emailHelp" />
        </div>
        <div class="mb-3">
          <label for="password" class="text-style form-label">Password</label>
          <input type="password" class="form-control border border-primary" name="password" id="password" />
        </div>
        <div class="mb-3 form-check">
          <input type="checkbox" class="form-check-input border border-primary" name="remember" id="remember" />
          <label class="text-style form-check-label" for="remember">Ingat Saya</label>
        </div>

        <div><a href="registrasi.php" class="text-style text-decoration-none">Belum punya akun?</a></div>

        <button type="submit" name="login" class="btn btn-primary mt-3">Login</button>
      </form>
    </div>
  </div>
  <!-- FORM -->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
    crossorigin="anonymous"></script>
  <script src="js/modal.js"></script>
</body>

</html>