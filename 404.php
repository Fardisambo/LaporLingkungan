<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan | Laporin Lingkungan</title>
    <link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            text-align: center;
            padding: 3rem 2rem;
        }
        .error-number {
            font-size: 8rem;
            font-weight: 900;
            color: #667eea;
            line-height: 1;
            margin-bottom: 1rem;
        }
        .error-title {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1rem;
        }
        .error-message {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 2rem;
        }
        .btn-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .btn-outline-secondary {
            border: 2px solid #6c757d;
            color: #6c757d;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        .btn-outline-secondary:hover {
            background: #6c757d;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.4);
        }
        .error-icon {
            font-size: 4rem;
            color: #667eea;
            margin-bottom: 2rem;
            opacity: 0.7;
        }
        @media (max-width: 768px) {
            .error-number {
                font-size: 6rem;
            }
            .error-title {
                font-size: 1.5rem;
            }
            .error-message {
                font-size: 1rem;
            }
            .error-card {
                padding: 2rem 1rem;
                margin: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <div class="error-card">
                        <div class="error-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <div class="error-number">404</div>
                        <h1 class="error-title">Halaman Tidak Ditemukan</h1>
                        <p class="error-message">
                            Maaf, halaman yang Anda cari tidak dapat ditemukan. 
                            Mungkin halaman tersebut telah dipindahkan atau dihapus.
                        </p>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="index.php" class="btn btn-custom me-md-2">
                                <i class="fas fa-home me-2"></i>Kembali ke Beranda
                            </a>
                            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 