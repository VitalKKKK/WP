<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" type="text/css" href="/assets/css/style.css">
    </head>
    </head>
    <body>
    <div class="header">
        <?= $class ?>
    </div>
    <div class="content">
        <form action="/" type="POST">
            <textarea name="comment[message]"></textarea>
            <input type="submit" value="send">
        </form>
        <div id="dcomment-block"></div>
    </div>
    <div class="footer">
        <script src="/assets/js/jquery-1.11.1.js"></script>
        <script src="/assets/js/core.js"></script>
    </div>
</html>
