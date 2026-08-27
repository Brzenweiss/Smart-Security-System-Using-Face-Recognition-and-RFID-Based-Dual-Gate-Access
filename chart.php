<?php
require 'koneksi.php';

header('Content-Type: application/json');

$data = [];

for ($i = 3; $i >= 0; $i--) {

    $tanggal = date("Y-m-d", strtotime("-$i days"));

    $query = mysqli_query($koneksi,
        "SELECT COUNT(*) as total 
         FROM log_masuk 
         WHERE DATE(waktu_masuk)='$tanggal'"
    );

    $row = mysqli_fetch_assoc($query);

    if($i==3) $label="H+3";
    elseif($i==2) $label="H+2";
    elseif($i==1) $label="H+1";
    else $label="Hari Ini";

    $data[] = [
        "hari"=>$label,
        "pengunjung"=>(int)$row['total']
    ];
}

echo json_encode($data);
exit;