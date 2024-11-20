<?php
// Start a user session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Site Variables
$siteName = "Dolphins are the best";
$contactEmail = "contact@example.com";
$contactPhone = "123-456-7890";

// Database connection setup
try {
    // Database connection variables
    $host = 'db';
    $dbname = 'web3400';
    $username = 'web3400';
    $password = 'password';
    $dsn = "mysql:host=$host;dbname=$dbname;charset=UTF8";

    // Create a PDO connection object
    $pdo = new PDO($dsn, $username, $password);

    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $dbname: " . $e->getMessage());
}

// Initialize messages array in session if not set
if (!isset($_SESSION['messages'])) {
    $_SESSION['messages'] = [];
}

// Function to display "time ago" format
function time_ago($datetime) {
    $time_ago = strtotime($datetime);
    $current_time = time();
    $time_difference = $current_time - $time_ago;

    $minutes = round($time_difference / 60);       
    $hours   = round($time_difference / 3600);     
    $days    = round($time_difference / 86400);    
    $weeks   = round($time_difference / 604800);   
    $months  = round($time_difference / 2629440);  
    $years   = round($time_difference / 31553280);

    if ($time_difference <= 60) return "Just now";
    if ($minutes <= 60) return $minutes == 1 ? "one minute ago" : "$minutes minutes ago";
    if ($hours <= 24) return $hours == 1 ? "an hour ago" : "$hours hours ago";
    if ($days <= 7) return $days == 1 ? "yesterday" : "$days days ago";
    if ($weeks <= 4.3) return $weeks == 1 ? "a week ago" : "$weeks weeks ago";
    if ($months <= 12) return $months == 1 ? "a month ago" : "$months months ago";
    return $years == 1 ? "one year ago" : "$years years ago";
}


