<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Error 404 - Página no encontrada | TAKAB</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/config.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body, html {
            background: #f5f7fa;
        }
        .content-area {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }



        
        .top-header {
            background: #14295e;
            border-bottom: 1.5px solid #e9eef5;
            padding: 20px 40px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }
        .top-header-user {
            display: flex;
            align-items: center;
            gap: 13px;
            font-weight: 700;
            color: #c0c2c7ff;
        }
        .logout-btn {
            color: #d32323;
            font-size: 1.23rem;
            margin-left: 12px;
            text-decoration: none;
            transition: color 0.15s;
        }
        .logout-btn:hover { color: #9b1818; }
        .error-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .error-icon {
            font-size: 6rem;
            color: #2563eb;
            margin-bottom: 10px;
        }
        .error-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 10px;
            color: #223264;
        }
        .error-desc {
            font-size: 1.3rem;
            color: #6476a8;
            margin-bottom: 32px;
        }
        .error-btn {
            display: inline-block;
            background: #2563eb;
            color: #fff;
            padding: 13px 32px;
            border-radius: 9px;
            font-size: 1.15rem;
            font-weight: 700;
            text-decoration: none;
            transition: background 0.17s;
        }
        .error-btn:hover { background: #1741a6; }
        @media (max-width:600px){
            .top-header{padding:12px 10px;}
            .error-title{font-size:1.5rem;}
            .error-icon{font-size:3rem;}
        }
    </style>
</head>
<body>
    <div class="content-area">
        
        <!-- Topbar -->
        <header class="top-header">
            
            <div class="top-header-user">
                
                <span><?= htmlspecialchars($_SESSION['nombre'] ?? 'TAKAB'); ?></span>
                <i class="fa-solid fa-user-circle"></i>
                <a href="logout.php" class="logout-btn" title="Cerrar sesión">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </a>
            </div>
        </header>
        <main class="error-main">
            
            <div class="error-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <div class="error-title">404 | Página no encontrada</div>
            <div class="error-desc">
                Lo sentimos, la página que buscas no existe o ha sido movida.<br>
                Si crees que es un error, contacta al administrador.
            </div>
            <a href="dashboard.php" class="error-btn"><i class="fa fa-home"></i> Ir al inicio</a>

            <footer>

                <div>
                    <img src="/assets/images/LogoTakab2.webp" alt="logo Takab">
                    
                </div>
            </footer>
        </main>

        
    </div>
</body>
</html>
