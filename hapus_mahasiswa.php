<?php
include 'header.php';
require 'function.php';

$npm = $_GET["id"];

if( hapus($npm) > 0 ) {
    echo "
        <script>
            alert('data berhasil dihapus');
            document.location.href = 'mahasiswa.php';
        </script>
    ";
} else {
    echo "
        <script>
            alert('data gagal dihapus!');
            document.location.href = 'mahasiswa.php';
        </script>
    ";
}

include 'footer.php';

