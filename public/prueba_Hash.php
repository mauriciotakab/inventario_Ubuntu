<?php
echo password_hash("123456", PASSWORD_DEFAULT);

echo "Rol actual: " . ($_SESSION['role'] ?? 'no logueado'); 
