<?php
if($_SERVER["HTTPS"] != "on")
{
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
}
if ((strpos($_SERVER['HTTP_HOST'], 'www.') === false))
{
    header('Location: http://www.'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
}
?>
<html>
<head>
  <link rel="icon" type="image/x-icon" href="./src/img/logo.png">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.1/css/bulma.min.css">
  <title>Cogestione 2023 - LeoneXIII</title>
  <meta name="description" content="Da segnare su tutti i calendari: 1 Marzo cogestione al Leone!" />
  <meta property="og:title" content="Cogestione 2023 - LeoneXIII" />
  <meta property="og:image" content="https://www.leonapp.it/src/img/banner.png" />

</head>

<body>
  <section class="section">
    <div class="container">
      <h1 class="title">Ciao 👋</h1>
      <h2 class="subtitle">Per continuare accedi con la tua email scolastica</h2>

      <script src="https://accounts.google.com/gsi/client" async defer></script>
      <div id="g_id_onload" data-client_id="626536855617-es6i5nct122k3tbci1qghpfj21k5mavf.apps.googleusercontent.com"
        data-ux_mode="redirect" data-login_uri="https://www.leonapp.it/auth.php">
      </div>
      <div class="g_id_signin" data-type="standard"></div>
    </div>
  </section>
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
</body>

</html>