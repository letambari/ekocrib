<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ekocrib";

// Create connection
global $con;
$con = new mysqli($servername, $username, $password, $dbname);

// session
session_start();
$website_name = "Ekocrib";
$website_url = "localhost/";



?>