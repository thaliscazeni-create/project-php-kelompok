<?php
  session_start();
  if (!isset($_SESSION["username"])) {
     header("Location: login.php");
  }

  include("connection.php");
  
  if (isset($_GET["message"])) {
      $message = $_GET["message"];
  }
     
  $query = "SELECT * FROM student ORDER BY nim ASC";
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Mahasiswa</title>
  <link href="assets/assets.css" rel="stylesheet" >
</head>
<body>
  <div class="container">
    <div id="header">
      <h1 id="logo">Daftar Data Mahasiswa</h1>
    </div>
    <hr>
    <nav>
      <ul>
        <li><a href="student_view.php">Tampil</a></li>
        <li><a href="student_add.php">Tambah</a>
        <li><a href="logout.php">Logout</a>
      </ul>
    </nav>
    <?php
      if (isset($message)) {
         echo "<div class='pesan'>$message</div>";
      }
    ?>
   <!-- ... kode sebelumnya (header, nav, dll) ... -->

    <table>
    <tr>
      <th>No</th>
      <th>NIM</th>
      <th>Nama</th>
      <th>Tempat Lahir</th>
      <th>Tanggal Lahir</th>
      <th>Fakultas</th>
      <th>Jurusan</th>
      <th>IPK</th>
      <th>Aksi</th> <!-- Tambahan Kolom Aksi -->
    </tr>
    <?php
      $result = mysqli_query($connection, $query);
      if(!$result) {
          die ("Query Error: ".mysqli_errno($connection)." - ".mysqli_error($connection));
      }

      $no = 1;
      while($data = mysqli_fetch_assoc($result)){ 
        $birth_date = strtotime($data["birth_date"]);
        $formatted_date = date("d-m-Y", $birth_date);
        
        echo "<tr>";
        echo "<td>".$no++."</td>";
        echo "<td>$data[nim]</td>";
        echo "<td>$data[name]</td>";
        echo "<td>$data[birth_city]</td>";
        echo "<td>$formatted_date</td>";
        echo "<td>$data[faculty]</td>";
        echo "<td>$data[department]</td>";
        echo "<td>$data[gpa]</td>";
        
        // Tambahan Tombol Aksi
        echo "<td>";
        echo "<a href='student_edit.php?nim=$data[nim]' class='btn-edit'>Edit</a> | ";
        echo "<a href='student_delete.php?nim=$data[nim]' class='btn-delete' onclick=\"return confirm('Yakin ingin menghapus data ini?');\">Hapus</a>";
        echo "</td>";
        
        echo "</tr>";
      }
      
      mysqli_free_result($result);
      mysqli_close($connection);
    ?>
    </table>
  </div>
</body>
</html>
