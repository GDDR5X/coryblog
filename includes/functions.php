<?php
// includes/functions.php

function format_date($date_string) {
    return date("F j, Y", strtotime($date_string));
}

function get_excerpt($content, $length = 150) {
    $text = strip_tags($content);
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        redirect('../admin/login.php');
    }
}
?>
