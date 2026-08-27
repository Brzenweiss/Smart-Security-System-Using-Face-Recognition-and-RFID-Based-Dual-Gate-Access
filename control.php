<?php

$pythonFilePath = 'python/deploy.py'; //  path yang sesuai
$pythonCommand  = 'python';

if ($_GET['action'] == 'start') {
    // Jalankan program Python
    $output = shell_exec("$pythonCommand $pythonFilePath");
    echo "Program Berjalan";
} elseif ($_GET['action'] == 'stop') {
    // Hentikan program Python
    // Sesuaikan perintah sesuai dengan kebutuhan Anda
    $output = shell_exec("pkill -f $pythonFilePath");
    echo "Program Berhenti";
} else {
    echo "Invalid action";
}

?>

