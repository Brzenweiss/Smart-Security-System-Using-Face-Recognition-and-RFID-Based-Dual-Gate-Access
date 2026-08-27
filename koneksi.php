<?php

$host       = "localhost";
$username   = "root";
$password   = "";
$database   = "smartsecurity";

$koneksi    = new mysqli($host, $username, $password, $database);

if($koneksi->error){
    die("koneksi gagal :')". $koneksi->connect_error);
}
