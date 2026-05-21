<!DOCTYPE html>
<html>
<head>
    <title>Login Admin</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            margin: 0;
            background: white;
        }

        .navbar {
            background-color: #f8c5ea;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h1 {
            margin: 0;
            font-size: 30px;
        }

        .navbar p {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
        }

        .container {
            text-align: center;
            margin-top: 60px;
        }

        .icon {
            width: 90px;
            height: 90px;
            background-color: #f8c5ea;
            border-radius: 50%;
            margin: 0 auto 20px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
        }

        input {
            width: 300px;
            padding: 10px;
            margin: 10px 0;
            border-radius: 10px;
            border: 1px solid #aaa;
            text-align: center;
        }

        .forgot {
            font-size: 12px;
            color: #f08bd6;
            margin-bottom: 15px;
            display: block;
        }

        button {
            width: 120px;
            padding: 10px;
            background-color: #f8c5ea;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #f4a6dc;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <h1>E - Library</h1>
        <p>Sistem Informasi Perpustakaan Digital</p>
    </div>

    <div class="container">
        <div class="icon">👤</div>

        <form method="POST" action="#">
            @csrf

            <input type="text" name="email" placeholder="Email / Username" required><br>

            <input type="password" name="password" placeholder="Password" required><br>

            <a href="#" class="forgot">Forget password?</a>

            <button type="submit">LOGIN</button>
        </form>
    </div>

</body>
</html>