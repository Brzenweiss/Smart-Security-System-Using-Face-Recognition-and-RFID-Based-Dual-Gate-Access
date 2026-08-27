<?php
include 'header.php';
require 'function.php';

// ambil data di URL
$npm = $_GET["id"];

// quey data mahasiswa berdasarkan id
$mhs = query("SELECT * FROM mahasiswa WHERE npm = $npm")[0];

// cek apakah tombol submit sudah ditekan
if (isset($_POST["submit"])) {

    // cek apakah data berhasil di ubah atau tidak
    if (ubah($_POST) > 0) {
        echo "
        <script>
            alert('data berhasil diubah!');
            document.location.href = 'mahasiswa.php';
        </script>
        ";
    } else {
        echo "
        <script>
            alert('data gagal diubah!');
            document.location.href = 'mahasiswa.php';
        </script>
        ";
    }

}

?>

<!-- Form -->
<div class="container">
    <section id="ubah">
        <div class="container mt-5">
            <div class="row text-center">
                <div class="col mb-3">
                    <h2>Ubah Data</h2>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <div class="row bg-info rounded-5 justify-content-center">
                        <div class="col-md-8">
                            <form action="" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="gambarLama" value="<?= $mhs["gambar"]; ?>">
                                <div class="mt-3 mb-3">
                                    <label for="npm" class="form-label">NPM</label>
                                    <input type="text" name="npm" class="form-control" id="npm" aria-describedby="npm"
                                        required value="<?= $mhs["npm"]; ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama</label>
                                    <input type="text" name="nama" class="form-control" id="nama"
                                        aria-describedby="nama" required value="<?= $mhs["nama"]; ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="text" name="email" class="form-control" id="email"
                                        aria-describedby="email" required value="<?= $mhs["email"]; ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="jurusan" class="form-label">Jurusan</label>
                                    <input type="text" name="jurusan" class="form-control" id="jurusan"
                                        aria-describedby="jurusan" required value="<?= $mhs["jurusan"]; ?>">
                                </div>
                                <div class="mb-5">
                                    <label for="gambar" class="form-label">Pilih Gambar : </label> <br>
                                    <img class="ms-5 mb-3" src="foto/<?= $mhs['gambar']; ?>">
                                    <input class="form-control" type="file" name="gambar" id="gambar">
                                </div>
                                <div class="mb-3">
                                    <input class="btn btn-primary" type="submit" name="submit" value="Simpan Perubahan">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
include 'footer.php';
?>