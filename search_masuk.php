<?php 
include 'header.php';
require 'function.php';
?>

<div class="container">
    <h1>Data Parkir Masuk</h1>
    <form method="post" action="search_masuk.php">
        <br>
        <div class="col-lg-4">
            <div class="form-group">
                <label class="ms-1 fw-bold" for="start_date">Batas Awal Tanggal</label>
                <input type="date" name="start_date" class="form-control" id="start_date">   
            </div>
        </div>
        <br>
        <div class="col-lg-4">
            <div class="form-group">
                <label class="ms-1 fw-bold" for="end_date">Batas Akhir Tanggal</label>
                <input type="date" name="end_date" class="form-control" id="end_date">   
            </div>
        </div>
        <br>
        <div class="col-lg-4">
            <div class="form-group">
                <input type="submit" name="submit_date" class="btn btn-primary" value="Filter">
                <a class="ms-5 btn btn-danger" href="hapus_parkir.php" onclick="
                    return confirm('Data akan dihapus Permanen, Pastikan telah melakukan Download melalui filter?');" role="button">Hapus Data</a>   
            </div>
        </div>
    </form>
    <br><br>

<?php 
$sql = "SELECT * from log_masuk";
$run = mysqli_query($koneksi, $sql);
    if (isset($_POST['submit_date'])) {
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        
        $start_date = date('Y-m-d H:i:s', strtotime($start_date . ' 00:00:00'));
        $end_date = date('Y-m-d H:i:s', strtotime($end_date . ' 23:59:59'));

        $query = mysqli_query($koneksi, 
        "SELECT * FROM log_masuk WHERE waktu_masuk BETWEEN '$start_date' AND '$end_date' ");
        $alert = 'Data akan dihapus Permanen, Apakah anda yakin untuk melanjutkan?';
        if (mysqli_num_rows($query)>0) {
            echo '<div class="mb-2">';
            echo '<form method="post" action="down_masuk_xls.php">';
            echo '<input type="hidden" name="start_date" value="' . $_POST['start_date'] . '">';
            echo '<input type="hidden" name="end_date" value="' . $_POST['end_date'] . '">';
            echo '<input type="submit" name="down_masuk_xls" class="btn btn-success" value="Export Excel">';
            echo '</form>';
            echo '</div>';


            foreach ($query as $value) :?>
                
                <div class="col-lg-12">
                    <table class="table table-dark table-striped">
                        <thead>
                            <th>No</th>
                            <th>ID</th>
                            <th>Foto</th>
                            <th>Waktu</th>
                        </thead>
                        <tbody>
                            <?php $i=1; ?>
                            <?php foreach ($query as $value) : ?>
                            <tr>
                                <td><?=$i;?></td>
                                <td><?=$value['uid'];?></td>
                                <td><img src="foto_pengunjung/<?=$value['foto'];?>" width="70"></td>
                                <td><?=$value['waktu_masuk'];?></td>
                                <?php $i++; ?>
                                
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
        <?php    
            endforeach;
        } else{
            echo "Data tidak ditemukan";
            }
    }else{

?>

    <table class="table table-dark table-striped">
        <thead>
            <th>No</th>
            <th>ID</th>
            <th>Foto</th>
            <th>Waktu</th>
        </thead>
        <tbody>
            <?php
                $i=1;
                // Inisialisasi nomor urut
                while ($row = mysqli_fetch_assoc($run)) {
                    $uid = $row['uid'];
                    $foto = $row['foto'];
                    $waktu_masuk = $row['waktu_masuk'];
            ?>
                <tr>
                    <td><?=$i;?></td>
                    <td><?=$uid;?></td>
                    <td><img src="foto_pengunjung/<?=$value['foto'];?>" width="70"></td>
                    <td><?=$waktu_masuk;?></td>
                </tr>
            <?php $i++; } ?>
        </tbody>
    </table>
</div>
<?php } ?>

<?php
include 'footer.php';
?>
