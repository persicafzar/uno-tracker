<?php
$password = 'admin123'; // رمز دلخواه شما
$hash = password_hash($password, PASSWORD_BCRYPT);
echo "Hash for '{$password}':\n{$hash}\n";