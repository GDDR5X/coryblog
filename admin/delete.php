<?php
// admin/delete.php
require_once '../includes/config.php';
require_once '../includes/functions.php';

session_start();
require_login();

$id = $_GET['id'] ?? 0;

if ($id) {
    delete_post($id);
}

redirect('index.php');
?>
