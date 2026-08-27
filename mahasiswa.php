<?php
include 'header.php';
require 'function.php';
$mahasiswa = query("SELECT * FROM mahasiswa");

// tombol cari ditekan
if( isset($_POST["cari"])) {
  $mahasiswa = cari($_POST["keyword"]);
}
?>

<div class="container">
    <h1>Data Mahasiswa</h1>
    <form class="w-50 mt-5" action="" method="post">
      <div class="input-group mb-3">
        <input type="text" class="form-control" name="keyword" size="50" autofocus 
        placeholder="Masukan Keyword Pencarian.." autocomplete="off" id="keyword">
        <button class="btn btn-success" type="submit" name="cari" id="tombol-cari">Cari!</button>
      </div>
    </form>

    <a class="mt-3 btn btn-primary" href="tambah_data.php" role="button">Tambah Data</a>
    <table class="table table-striped">
  <thead>
    <tr>
      <th scope="col">NO</th>
      <th scope="col">Aksi</th>
      <th scope="col">NPM</th>
      <th scope="col">Nama</th>
      <th scope="col">Email</th>
      <th scope="col">Jurusan</th>
      <th scope="col">Foto</th>
    </tr>
  </thead>
  <?php $i = 1; ?>
  <?php foreach($mahasiswa as $row) : ?>
  <tbody>
    <tr>
      <td><?php echo $i; ?></td>
      <td>
          <a class="btn btn-primary" href="ubah_mahasiswa.php?id=<?=$row["npm"]; ?>" role="button">Ubah</a>
          <a class="btn btn-danger" href="hapus_mahasiswa.php?id=<?=$row["npm"]; ?>" onclick="
          return confirm('Data akan dihapus Permanen, Apakah anda yakin untuk melanjutkan?');" role="button">Hapus</a>
        </td>
      <td><?php echo $row["npm"]; ?></td>
      <td><?php echo $row["nama"]; ?></td>
      <td><?php echo $row["email"]; ?></td>
      <td><?php echo $row["jurusan"]; ?></td>
      <td><img src="foto/<?= $row["gambar"]; ?>" width="50" alt=""></td>
    </tr>
  </tbody>
  <?php $i++; ?>
  <?php endforeach; ?>
</table>

</div>

<?php 
include 'footer.php';
?>