<?php
include 'header.php';
require 'function.php';
?>
<h1>Kontrol Panel</h1>
<div class="container text-center">

    <divs class="mt-4 mx-auto w-50">
        <img src="img/sistem.PNG" alt="parkir">
    </divs>
    <div class="mx-auto w-25">
        <button class="btn btn-success" id="startBtn">Start Program</button>
        <button class="btn btn-danger" id="stopBtn">Stop Program</button>
    </div>
    <div class="mt-5 mx-auto w-50">
        <img class="rounded" src="img/ktp.png" alt="ktp" width="300" height="200">
    </div>
    <div class="mx-auto w-25">
        <a class="mt-3 btn btn-primary" href="isi_nama.php" role="button">Isi Identitas Kartu</a>
    </div>
</div>

<?php
include 'footer.php';
?>