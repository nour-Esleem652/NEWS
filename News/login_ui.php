<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
</head>
<body>
    <center>
        <h2>Login Page</h2>
        <form action="login_logic.php" method="post">
            <label>email</label>
            <input type="email" name="email" id="email">
            <br>
            <label>password</label>
            <input type="password" name="password" id="password">
            <br>
            <input type="submit" name="login" value="login">
        </form>
    </center>
</body>
</html>