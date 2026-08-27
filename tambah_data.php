<?php 
include 'header.php';
require 'function.php';
// cek apakah tombol submit sudah ditekan
if ( isset($_POST["submit"] )) {

    // cek apakah data berhasil di tambahkan atau tidak
    if ( tambah($_POST) > 0 ) {
        echo "
        <script>
            alert('data berhasil ditambahakan!');
            document.location.href = 'mahasiswa.php';
        </script>
        ";
    } else {
        echo "
        <script>
            alert('data gagal ditambahakan!');
            document.location.href = 'mahasiswa.php';
        </script>
        ";
    }

}

?>

<div class="container">
<h1>Tambah Data Mahasiswa</h1>

<form action="" method="post" enctype="multipart/form-data">
<div class="border border-primary rounded-3 bg-light bg-gradient mx-auto mt-5 p-3 ms-3 me-3">
    <div class="mb-3">
        <label for="npm">NPM : </label>
        <input type="text" class="form-control" name="npm" id="npm" required autocomplete="off">
    </div>

    <div class="mb-3">
        <label for="nama" class="form-label">Nama : </label>
        <input type="text" class="form-control" name="nama" id="nama" required autocomplete="off">
    </div>
    
    <div class="mb-3">
        <label for="email" class="form-label">Email : </label>
        <input type="text" class="form-control" name="email" id="email" required autocomplete="off">
    </div>

    <div class="mb-3">
        <label for="jurusan" class="form-label">Jurusan : </label>
        <input type="text" class="form-control" name="jurusan" id="jurusan" required autocomplete="off">
    </div>

    <div class="input-group mb-3">
        <label for="gambar" class="input-group-text">Upload Foto</label>
        <input type="file" class="form-control" name="gambar" id="gambar">
    </div>
        
    <button type="submit" class="btn btn-primary" name="submit">Tambah Data</button>


</form>
</div>
</div>

<?php 
    include 'footer.php';
?>