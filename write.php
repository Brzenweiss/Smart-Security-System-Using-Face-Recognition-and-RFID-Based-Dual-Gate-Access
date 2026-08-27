<?php
$data = $_GET['data'];
exec("echo 'WRITE:$data' > COM4"); // Ganti COM dengan port serial yang digunakan mikrokontroller
echo "Data sent to Arduino: $data";
?>