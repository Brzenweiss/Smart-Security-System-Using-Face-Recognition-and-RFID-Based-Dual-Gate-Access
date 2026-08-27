<?php
include 'header.php';
?>


<div class="container">
    <h1>Data Keluar Hari Ini</h1>
    <table class="table table-dark table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>ID</th>
                <th>Foto</th>
                <th>Waktu</th>
            </tr>
        </thead>
        <tbody>
            <?php
            require 'function.php';
            echo data_keluar();
            ?>
        </tbody>
    </table>
</div>

<?php
include 'footer.php';
?>