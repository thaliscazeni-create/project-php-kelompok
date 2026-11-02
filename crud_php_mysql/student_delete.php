<?php
  session_start();
  if (!isset($_SESSION["username"])) {
     header("Location: login.php");
     exit;
  }

  include("connection.php");

  if (isset($_GET["nim"])) {
    $nim = $_GET["nim"];
    $nim = mysqli_real_escape_string($connection, $nim);

    // Ambil nama untuk pesan konfirmasi
    $query = "SELECT name FROM student WHERE nim='$nim'";
    $result = mysqli_query($connection, $query);
    $data = mysqli_fetch_assoc($result);

    if ($data) {
        $name = $data['name'];
        
        // Hapus data
        $query_delete = "DELETE FROM student WHERE nim='$nim'";
        $result_delete = mysqli_query($connection, $query_delete);

        if ($result_delete) {
            $message = "Data mahasiswa dengan nama = \"<b>$name</b>\" berhasil dihapus";
            $message = urlencode($message);
            header("Location: student_view.php?message={$message}");
            exit;
        } else {
            die ("Query gagal dijalankan: ".mysqli_errno($connection). " - ".mysqli_error($connection));
        }
    } else {
        header("Location: student_view.php");
        exit;
    }
  } else {
    header("Location: student_view.php");
    exit;
  }

  mysqli_close($connection);
?>