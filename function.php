<?php
require $_SERVER['DOCUMENT_ROOT'] . '/smartsecurity/koneksi.php';
;

function query($query)
{
    global $koneksi;
    $result = mysqli_query($koneksi, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

function registrasi($data)
{
    global $koneksi;

    $username = strtolower(stripslashes($data["username"]));
    $password = mysqli_real_escape_string($koneksi, $data["password"]);
    $password2 = mysqli_real_escape_string($koneksi, $data["password2"]);

    // cek username sudah ada atau belum
    $result = mysqli_query($koneksi, "SELECT username FROM user WHERE username = '$username'");

    if (mysqli_fetch_assoc($result)) {
        echo "<script>
                alert('username sudah terdaftar')
            </script>";
        return false;
    }

    // cek konfirmasi password
    if ($password !== $password2) {
        echo "<script>
                alert('konfirmasi password tidak sesuai')
             </script>";
        return false;
    }

    //enskripsi password
    $password = password_hash($password, PASSWORD_DEFAULT);

    // tambahkan userbaru ke database
    mysqli_query($koneksi, "INSERT INTO user VALUES('', '$username', '$password')");

    return mysqli_affected_rows($koneksi);
}

function data_masuk()
{
    global $koneksi;
    // Dapatkan tanggal hari ini dalam format yyyy-mm-dd
    $today = date("Y-m-d");

    // Query untuk mengambil data hanya untuk hari ini
    $sql = "SELECT * FROM log_masuk WHERE DATE(waktu_masuk) = '$today'";
    $result = $koneksi->query($sql);

    // Inisialisasi nomor urut
    $counter = 1;

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $counter . "</td>";  // Gunakan nomor urut biasa
            echo "<td>" . $row["uid"] . "</td>";
            ##echo "<td>" . $row["nama"] . "</td>";
            echo "<td> <img src='foto_pengunjung/" . $row["foto"] . "' width='70'></td>";
            echo "<td>" . $row["waktu_masuk"] . "</td>";
            echo "</tr>";

            // Tingkatkan nomor urut
            $counter++;
        }
    } else {
        echo "<tr><td colspan='4'>Tidak ada data untuk hari ini.</td></tr>";
    }

    $koneksi->close();
}

function data_keluar()
{
    global $koneksi;

    // Dapatkan tanggal hari ini dalam format yyyy-mm-dd
    $today = date("Y-m-d");

    // Query untuk mengambil data hanya untuk hari ini
    $sql = "SELECT * FROM log_keluar WHERE DATE(waktu_keluar) = '$today'";
    $result = $koneksi->query($sql);

    // Inisialisasi nomor urut
    $counter = 1;

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $counter . "</td>";  // Gunakan nomor urut biasa
            echo "<td>" . $row["uid"] . "</td>";
            ##echo "<td>" . $row["nama"] . "</td>";
            echo "<td> <img src='foto_pengunjung/" . $row["foto"] . "' width='70'></td>";
            echo "<td>" . $row["waktu_keluar"] . "</td>";
            echo "</tr>";

            // Tingkatkan nomor urut
            $counter++;
        }
    } else {
        echo "<tr><td colspan='4'>Tidak ada data untuk hari ini.</td></tr>";
    }

    $koneksi->close();
}

function tambah($data)
{
    global $koneksi;

    $npm = htmlspecialchars($data["npm"]);
    $nama = htmlspecialchars($data["nama"]);
    $email = htmlspecialchars($data["email"]);
    $jurusan = htmlspecialchars($data["jurusan"]);

    // upload gambar
    $gambar = upload();
    if (!$gambar = upload()) {
        return false;
    }

    // query insert data
    $query = "INSERT INTO mahasiswa
                VALUES
                ('$npm', '$nama', '$email', '$jurusan', '$gambar')";

    mysqli_query($koneksi, $query);

    return mysqli_affected_rows($koneksi);
}

function upload()
{

    $namaFile = $_FILES['gambar']['name'];
    $ukuranFile = $_FILES['gambar']['size'];
    $error = $_FILES['gambar']['error'];
    $tmpName = $_FILES['gambar']['tmp_name'];

    // cek apakah tidak ada gambar yang diupload
    if ($error === 4) {
        echo "<script>
                alert('pilih gambar terlebih dahulu!');
                </script>";
        return false;
    }

    // cek apakah yang diupload adalah gambar
    $ekstensiGambarValid = ['jpg', 'jpeg', 'png'];
    $ekstensiGambar = explode('.', $namaFile);
    $ekstensiGambar = strtolower(end($ekstensiGambar));
    if (!in_array($ekstensiGambar, $ekstensiGambarValid)) {
        echo "<script>
                alert('yang anda upload bukan gambar');
                </script>";
        return false;
    }

    // cek jika ukurannya terlalu besar
    if ($ukuranFile > 1000000) {
        echo "<script>
                alert('ukuran gambar terlalu besar');
                </script>";
        return false;
    }

    // lolos pengecekan, gambar siap diupload
    // generate nama gambar baru
    $t = time();
    $namaFileBaru = date("Y_m_d_H_i_s", $t);
    $namaFileBaru .= '.';
    $namaFileBaru .= $ekstensiGambar;

    move_uploaded_file($tmpName, 'foto_pengunjung/' . $namaFileBaru);
    return $namaFileBaru;

}

function hapus($npm)
{
    global $koneksi;
    mysqli_query($koneksi, "DELETE FROM mahasiswa WHERE npm = $npm");

    return mysqli_affected_rows($koneksi);
}

function ubah($data)
{
    global $koneksi;
    $npm = htmlspecialchars($data["npm"]);
    $nama = htmlspecialchars($data["nama"]);
    $email = htmlspecialchars($data["email"]);
    $jurusan = htmlspecialchars($data["jurusan"]);
    $gambarLama = htmlspecialchars($data["gambarLama"]);

    // cek apakah user pilih gambar baru atau tidak
    if ($_FILES['gambar']['error'] === 4) {
        $gambar = $gambarLama;
    } else {
        $gambar = upload();
    }

    // query update data dengan klausa WHERE
    $query = "UPDATE mahasiswa SET
                nama    = '$nama',
                email   = '$email',
                jurusan = '$jurusan',
                gambar  = '$gambar'
            WHERE npm   = '$npm'";

    mysqli_query($koneksi, $query);

    return mysqli_affected_rows($koneksi);
}

function cari($keyword)
{
    $query = "SELECT * FROM mahasiswa
                WHERE
              nama LIKE '%$keyword%' OR
              npm LIKE  '%$keyword%' OR
              email LIKE '%$keyword%' OR
              jurusan LIKE  '%$keyword%'
            ";
    return query($query);
}

function download_data_keluar()
{
    global $koneksi;
    if (isset($_POST['down_keluar_xls'])) {
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        $start_date = date('Y-m-d H:i:s', strtotime($start_date . ' 00:00:00'));
        $end_date = date('Y-m-d H:i:s', strtotime($end_date . ' 23:59:59'));

        $query = mysqli_query(
            $koneksi,
            "SELECT * FROM log_keluar WHERE waktu_keluar BETWEEN '$start_date' AND '$end_date' "
        );

        if (mysqli_num_rows($query) > 0) {
            // Header Excel
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="Data Pengunjung Keluar.xls"');

            echo '<html>';
            echo '<head><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8"></head>';
            echo '<body>';
            echo '<style>
                    table{border-collapse:collapse;}
                    td{border:1px solid black;text-align:center;vertical-align:middle;}
                    tr{height:80px;}
                    </style>';
            
            echo '<table>';

            // Tulis Header Excel
            echo '<tr><td>No</td><td>ID</td><td>Foto</td><td>Waktu</td></tr>';

            $i = 1; // Start from row 2
            foreach ($query as $value) {
                // Tulis Data Excel
                echo '<tr>';
                echo '<td>' . $i . '</td>';
                echo '<td>' . $value['uid'] . '</td>';
                echo '<td style="width:90px">
                        <img src="http://localhost/smartsecurity/foto_pengunjung/'.$value['foto'].'" width="70" height="70">
                        </td>';
                echo '<td style="width:150px">'.$value['waktu_keluar'].'</td>';
                echo '</tr>';
                $i++;
            }

            echo '</table>';
            echo '</body>';
            echo '</html>';

            exit;
        }
    }
}

function download_data_masuk()
{
    global $koneksi;
    if (isset($_POST['down_masuk_xls'])) {
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        $start_date = date('Y-m-d H:i:s', strtotime($start_date . ' 00:00:00'));
        $end_date = date('Y-m-d H:i:s', strtotime($end_date . ' 23:59:59'));

        $query = mysqli_query(
            $koneksi,
            "SELECT * FROM log_masuk WHERE waktu_masuk BETWEEN '$start_date' AND '$end_date' "
        );

        if (mysqli_num_rows($query) > 0) {
            // Header Excel
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="Data Pengunjung Masuk.xls"');

            echo '<html>';
            echo '<head><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8"></head>';
            echo '<body>';
            echo '<style>
                    table{border-collapse:collapse;}
                    td{border:1px solid black;text-align:center;vertical-align:middle;}
                    tr{height:80px;}
                    </style>';
            echo '<table>';

            // Tulis Header Excel
            echo '<tr><td>No</td><td>ID</td><td>Foto</td><td>Waktu</td></tr>';

            $i = 1; // Start from row 2
            foreach ($query as $value) {
                // Tulis Data Excel
                echo '<tr>';
                echo '<td>' . $i . '</td>';
                echo '<td>' . $value['uid'] . '</td>';
                echo '<td style="width:90px">
                        <img src="http://localhost/smartsecurity/foto_pengunjung/'.$value['foto'].'" width="70" height="70">
                        </td>';
                echo '<td style="width:150px">' . $value['waktu_masuk'] . '</td>';
                echo '</tr>';
                $i++;
            }

            echo '</table>';
            echo '</body>';
            echo '</html>';

            exit;
        }
    }
}

