<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" type="text/css" href="/assets/css/style.css">
    </head>
    <body>
        <div class="header">
            
        </div>
        <div class="content">
            <div>
                <?php if (!$userID) : ?>
                <a href="#" class="show-login">Login</a> <a href="#" class="show-registration">Registration</a>
                <?php else : ?>
                    <a href="/user/logout">Logout</a>
                <?php endif; ?>
            </div>
            <div class="login-registration-block">
                <div class="login-block">
                    <form action="#" class="login">
                        <input type="text" name="email">
                        <input type="password" name="password">
                        <input type="submit" value="Login">
                    </form>
                </div>
                <div class="registration-block">
                    <form action="#"  class="registration">
                        <input type="text" name="email">
                        <input type="password" name="password">
                        <input type="password" name="confirm_password">
                        <input type="submit" value="Registration">
                    </form>
                </div>
            </div>

            <div id="dcomment-block"></div>
        </div>
        <div class="footer">
            <script src="/assets/js/jquery-1.11.1.js"></script>
            <script src="/assets/js/core.js"></script>
        </div>
    </body>
</html>
