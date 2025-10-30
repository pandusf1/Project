<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="aset/css/login.css">
</head>
<body>
  <div class="login-container">
    <div class="logo">HC</div>
    <h2>Hitech Computer</h2>
    <p>Sistem Manajemen Toko Hitech Computer</p>

    <form action="login.php" method="post">
      <label for="username">Username</label>
      <input style="width:93%;" type="text" id="username" name="username" placeholder="Masukkan username" required>

      <label for="password">Password</label>
      <input style="width:93%;" type="password" id="password" name="password" placeholder="Masukkan password" required>

      <button type="submit">Masuk</button>
    </form>
  </div>
</body>
</html>
