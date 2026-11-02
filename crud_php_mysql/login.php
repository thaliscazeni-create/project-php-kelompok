<?php
  if (isset($_POST["submit"])) {
    $username = htmlentities(strip_tags(trim($_POST["username"])));
    $password = htmlentities(strip_tags(trim($_POST["password"])));

    $error_message = "";

    if (empty($username)) {
      $error_message .= "- Username belum diisi <br>";
    }

    if (empty($password)) {
      $error_message .= "- Password belum diisi <br>";
    }

    include("connection.php");

    $username = mysqli_real_escape_string($connection, $username);
    $password = mysqli_real_escape_string($connection, $password);

    $password_sha1 = sha1($password);

    $query = "
      SELECT * FROM admin 
      WHERE username = '$username' AND password = '$password_sha1'";
    $result = mysqli_query($connection, $query);

    if(mysqli_num_rows($result) == 0 )  {
      $error_message .= "- Username dan/atau Password tidak sesuai";
    }

    mysqli_free_result($result);
    mysqli_close($connection);

    if ($error_message === "") {
      session_start();
      $_SESSION["username"] = $username;
      header("Location: student_view.php");
    }
  }
  else {
    $error_message = "";
    $username = "";
    $password = "";
  }
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login Data Mahasiswa</title>
  <!-- 1. Panggil CSS -->
  <link href="assets/assets.css" rel="stylesheet">
  
  <!-- 2. Tambahan CSS khusus agar form login ada di tengah layar -->
  <style>
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh; /* Memenuhi tinggi layar */
      margin: 0;
      background-color: #f0f2f5; /* Warna latar belakang */
    }
    
    .login-container {
      width: 100%;
      max-width: 400px; /* Lebar maksimal form login */
      background: #ffffff;
      padding: 40px 30px;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }

    .login-container h1 {
      text-align: center;
      margin-bottom: 25px;
      color: #1a202c;
      font-size: 24px;
    }

    .login-container fieldset {
      border: none; /* Hilangkan garis fieldset agar lebih modern */
      padding: 0;
      margin: 0;
      background: transparent;
    }

    .login-container legend {
      display: none; /* Sembunyikan tulisan "Login" di legend */
    }

    .login-container input[type="submit"] {
      width: 100%; /* Tombol jadi lebar penuh */
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <!-- Bungkus dengan class login-container -->
  <div class="login-container">
    <h1>Data Mahasiswa</h1>
    
    <?php
      if ($error_message !== "")
        echo "<div class='error'>$error_message</div>";
    ?>
    
    <form action="login.php" method="post">
      <fieldset>
        <legend>Login</legend>
        <p>
          <label for="username">Username : </label>
          <!-- Hapus atribut value agar tidak otomatis terisi teks aneh -->
          <input type="text" name="username" id="username" value="<?php echo $username ?>" placeholder="Masukkan username">
        </p>
        <p>
          <label for="password">Password : </label>
          <input type="password" name="password" id="password" placeholder="Masukkan password">
        </p>
        <p>
          <input type="submit" name="submit" value="Log In">
        </p>
      </fieldset>
    </form>
  </div>
</body>
</html>