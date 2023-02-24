<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.1/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        #notifica {
            height: 100%; display: flex; align-items: center; justify-content: center; position: fixed;
            margin-left: 80px;
            margin-right:  80px;
            z-index: 10;
        }

</style>
</head>

<body>
    <?php
    if(isset($_COOKIE['error'])) {
        echo '<div id="notifica">
        <div class="notification is-danger is-light">
            <button class="delete" onClick="document.getElementById(\'notifica\').style.display=\'none\'"></button>
            '.$_COOKIE['error'].'
        </div>
        </div>';
    }
    unset($_COOKIE['error']);
    ?>
<div id="notifica">
    <div class="notification is-danger is-light">
        <button class="delete" onClick="document.getElementById('notifica').style.display='none'"></button>
        Non &egrave; stato possibile accedere con il tuo account Google. Riprova pi&ugrave; tardi.
    </div>
    </div>
</body>