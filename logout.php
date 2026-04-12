<?php
// logout
// session startt
session_start();

// remove all session variables
session_unset();

// destroy the seesion cookie and data
session_destroy();

header("Location: ./html/login.html");
exit;