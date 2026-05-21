<!DOCTYPE html>
<html>
<head>
    <title>E-Library</title>

    <style>
        body{
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }

        .navbar{
            background-color: #ffffff;
            padding: 15px 30px;
            border-bottom: 1px solid #ddd;
        }

        .navbar h1{
            margin: 0;
            font-size: 24px;
            color: #333;
        }

        .navbar p{
            margin: 5px 0 0;
            font-size: 14px;
            color: #666;
        }

        .container{
            width: 350px;
            margin: 60px auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
        }

        .logo{
            width: 100px;
            margin-bottom: 20px;
        }

        .title{
            font-size: 22px;
            margin-bottom: 10px;
            color: #333;
        }

        .subtitle{
            font-size: 14px;
            color: #777;
            margin-bottom: 30px;
        }

        .btn{
            display: block;
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            background-color: #4a90e2;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }

        .btn:hover{
            background-color: #357bd8;
        }
    </style>
</head>

<body>

    <div class="navbar">
        <h1>E-Library</h1>
        <p>Sistem Informasi Perpustakaan Digital</p>
    </div>

    <div class="container">

        <img src="{{ asset('images/logo.png') }}" class="logo" alt="Logo">

        <div class="title">
            Selamat Datang
        </div>

        <div class="subtitle">
            Silakan pilih login
        </div>

        <a href="{{ route('admin.login') }}" class="btn">
            Login Admin
        </a>

        <a href="{{ route('register') }}" class="btn">
            Login Anggota
        </a>

    </div>

</body>
</html>