<?php
  session_start();
  if (!isset($_SESSION["username"])) {
     header("Location: login.php");
     exit;
  }

  include("connection.php");

  // Ambil data berdasarkan NIM yang dikirim via URL
  if (isset($_GET["nim"])) {
    $nim = $_GET["nim"];
    $nim = mysqli_real_escape_string($connection, $nim);
    $query = "SELECT * FROM student WHERE nim='$nim'";
    $result = mysqli_query($connection, $query);
    $data = mysqli_fetch_assoc($result);
    
    if (!$data) {
        die("Data mahasiswa tidak ditemukan.");
    }
  } else {
    header("Location: student_view.php");
    exit;
  }

  if (isset($_POST["submit"])) {
    $nim_baru = htmlentities(strip_tags(trim($_POST["nim"])));
    $name = htmlentities(strip_tags(trim($_POST["name"])));
    $birth_city = htmlentities(strip_tags(trim($_POST["birth_city"])));
    $faculty = htmlentities(strip_tags(trim($_POST["faculty"])));
    $department = htmlentities(strip_tags(trim($_POST["department"])));
    $gpa = htmlentities(strip_tags(trim($_POST["gpa"])));
    $birth_date = htmlentities(strip_tags(trim($_POST["birth_date"])));
    $birth_month = htmlentities(strip_tags(trim($_POST["birth_month"])));
    $birth_year = htmlentities(strip_tags(trim($_POST["birth_year"])));

    $error_message="";

    if (empty($nim_baru)) {
      $error_message .= "- NIM belum diisi <br>";
    } else if (!preg_match("/^[0-9]{8}$/",$nim_baru) ) {
      $error_message .= "- NIM harus berupa 8 digit angka <br>";
    }

    // Cek jika NIM diubah, pastikan NIM baru belum dipakai
    if ($nim_baru != $nim) {
        $query_cek = "SELECT * FROM student WHERE nim='$nim_baru'";
        $result_cek = mysqli_query($connection, $query_cek);
        if (mysqli_num_rows($result_cek) > 0) {
            $error_message .= "- NIM yang baru sudah digunakan <br>";
        }
    }

    if (empty($name)) $error_message .= "- Nama belum diisi <br>";
    if (empty($birth_city)) $error_message .= "- Tempat lahir belum diisi <br>";
    if (empty($department)) $error_message .= "- Jurusan belum diisi <br>";
    if (!is_numeric($gpa) OR ($gpa <=0)) $error_message .= "- IPK harus diisi dengan angka";

    $select_ftib=""; $select_fteic="";
    if ($faculty == 'FTIB') $select_ftib = "selected";
    if ($faculty == 'FTEIC') $select_fteic = "selected";

    if ($error_message === "") {
      $nim_baru = mysqli_real_escape_string($connection, $nim_baru);
      $name = mysqli_real_escape_string($connection, $name);
      $birth_city = mysqli_real_escape_string($connection, $birth_city);
      $faculty = mysqli_real_escape_string($connection, $faculty);
      $department = mysqli_real_escape_string($connection, $department);
      $birth_date = mysqli_real_escape_string($connection, $birth_date);
      $birth_month = mysqli_real_escape_string($connection, $birth_month);
      $birth_year  = mysqli_real_escape_string($connection, $birth_year);
      $gpa = (float) $gpa;

      $birth_date_full = $birth_year."-".$birth_month."-".$birth_date;

      $query = "UPDATE student SET 
                  nim='$nim_baru', 
                  name='$name', 
                  birth_city='$birth_city', 
                  birth_date='$birth_date_full', 
                  faculty='$faculty', 
                  department='$department', 
                  gpa=$gpa 
                WHERE nim='$nim'";
      
      $result = mysqli_query($connection, $query);

      if($result) {
        $message = "Data mahasiswa dengan nama = \"<b>$name</b>\" berhasil di-update";
        $message = urlencode($message);
        header("Location: student_view.php?message={$message}");
        exit;
      } else {
        die ("Query gagal dijalankan: ".mysqli_errno($connection). " - ".mysqli_error($connection));
      }
    }
  } else {
    // Inisialisasi variabel dari database saat halaman pertama kali dibuka
    $nim_baru = $data['nim'];
    $name = $data['name'];
    $birth_city = $data['birth_city'];
    $faculty = $data['faculty'];
    $department = $data['department'];
    $gpa = $data['gpa'];
    
    // Pecah tanggal lahir
    list($birth_year, $birth_month, $birth_date) = explode("-", $data['birth_date']);
    
    $select_ftib = ($faculty == 'FTIB') ? "selected" : "";
    $select_fteic = ($faculty == 'FTEIC') ? "selected" : "";
    $error_message = "";
  }

  $arr_month = [
    "1"=>"Januari", "2"=>"Februari", "3"=>"Maret", "4"=>"April", "5"=>"Mei", "6"=>"Juni",
    "7"=>"Juli", "8"=>"Agustus", "9"=>"September", "10"=>"Oktober", "11"=>"Nopember", "12"=>"Desember"
  ];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Data Mahasiswa</title>
  <!-- TAMBAHKAN INI: Memanggil CSS -->
  <link href="assets/assets.css" rel="stylesheet" >
</head>
<body>
  <!-- TAMBAHKAN INI: Bungkus dengan class container -->
  <div class="container">
    <div id="header">
      <h1 id="logo">Data Mahasiswa</h1>
    </div>
    <hr>
    <nav>
      <ul>
        <li><a href="student_view.php">Tampil</a></li>
        <li><a href="student_add.php">Tambah</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </nav>
    <h2>Edit Data Mahasiswa</h2>
    <?php
      if ($error_message !== "") {
          echo "<div class='error'>$error_message</div>";
      }
    ?>
    <form id="form_mahasiswa" action="student_edit.php?nim=<?php echo $nim; ?>" method="post">
      <fieldset>
        <legend>Edit Mahasiswa</legend>
        
        <p>
          <label for="nim">NIM : </label>
          <input type="text" name="nim" id="nim" value="<?php echo $nim_baru ?>" placeholder="Contoh: 12345678"> (8 digit angka)
        </p>
        <p>
          <label for="name">Nama : </label>
          <input type="text" name="name" id="name" value="<?php echo $name ?>">
        </p>
        <p>
          <label for="birth_city">Tempat Lahir : </label>
          <input type="text" name="birth_city" id="birth_city" value="<?php echo $birth_city ?>">
        </p>
        <p>
          <label for="birth_date" >Tanggal Lahir : </label>
            <select name="birth_date" id="birth_date">
              <?php
                for ($i = 1; $i <= 31; $i++) {
                  $selected = ($i == $birth_date) ? "selected" : "";
                  echo "<option value='$i' $selected>" . str_pad($i, 2, "0", STR_PAD_LEFT) . "</option>";
                }
              ?>
            </select>
            <select name="birth_month">
              <?php
                foreach ($arr_month as $key => $value) {
                  $selected = ($key == $birth_month) ? "selected" : "";
                  echo "<option value='$key' $selected>$value</option>";
                }
              ?>
            </select>
            <select name="birth_year">
              <?php
                for ($i = 1990; $i <= 2005; $i++) {
                  $selected = ($i == $birth_year) ? "selected" : "";
                  echo "<option value='$i' $selected>$i</option>";
                }
              ?>
            </select>
        </p>
        <p>
          <label for="faculty" >Fakultas : </label>
            <select name="faculty" id="faculty">
              <option value="FTIB" <?php echo $select_ftib ?>>FTIB </option>
              <option value="FTEIC" <?php echo $select_fteic ?>>FTEIC</option>
            </select>
        </p>
        <p>
          <label for="department">Jurusan : </label>
          <input type="text" name="department" id="department" value="<?php echo $department ?>">
        </p>
        <p>
          <label for="gpa">IPK : </label>
          <input type="text" name="gpa" id="gpa" value="<?php echo $gpa ?>" placeholder="Contoh: 2.75">
        </p>
      </fieldset>
      <br>
      <p>
        <input type="submit" name="submit" value="Update Data">
      </p>
    </form>
  </div> <!-- TUTUP DIV CONTAINER DI SINI -->
</body>
</html>
<?php
  mysqli_close($connection);
?>