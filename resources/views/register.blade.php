<!DOCTYPE html>
<html>
<head>
    <title>E-Library</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }

        .navbar {
            background: white;
            padding: 20px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        .container {
            width: 350px;
            margin: 70px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        img {
            width: 100px;
            margin-bottom: 20px;
        }

        a {
            display: block;
            padding: 12px;
            margin: 12px 0;
            background: #4a90e2;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        a:hover {
            background: #357bd8;
        }
    </style>
</head>
<body>

<div class="navbar">
    <h1>E-Library</h1>
    <p>Sistem Informasi Perpustakaan Digital</p>
</div>

<div class="container">
    <img src="{{ asset('images/logo.png') }}" alt="Logo">

    <h2>Selamat Datang</h2>
    <p>Silakan pilih menu</p>

    <a href="{{ route('login') }}">Login Anggota</a>
    <a href="{{ route('register') }}">Register Anggota</a>
    <a href="{{ route('admin.login') }}">Login Admin / Petugas</a>
</div>

</body>
</html>