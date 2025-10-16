<?php
// Archivo: /config/config.php

//define('DB_HOST', '192.168.56.3');    //servidor virtual 
define('DB_HOST', 'localhost:3306');
//define('DB_NAME', 'takab_inventario');
//define('DB_USER', 'mau');
//define('DB_PASS', 'mau');           //Cambia esto a la contraseña real

//define('DB_HOST', '192.168.1.253');       //servidor local
define('DB_NAME', 'takab_inventario');
define('DB_USER', 'inventario_user');
define('DB_PASS', 'AdminTakab123');           //Cambia esto a la contraseña real
define('DB_CHARSET', 'utf8mb4'); 

// Opcional: Puerto (para XAMPP/WAMP suele ser 3306)
define('DB_PORT', 3306);

// Opciones extra
define('APP_NAME', 'Sistema de Inventario TAKAB');
define('APP_LANG', 'es_MX');
date_default_timezone_set('America/Mexico_City'); // esto es solo para PHP


