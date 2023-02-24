<?php 

error_reporting(-1);
ini_set('display_errors', 'On');

include 'mysqli.php';

//Select distinct ora from attivita and get how many there are
$sql = "SELECT DISTINCT ora FROM attivita";
$result = $conn->query($sql);
$many_blocks = $result->num_rows;
$good_selections = array();
for($i=1; $i<=$many_blocks; $i++) {
    $good_selections[$i] = 2;
}

//make a function that sets the cookie "resume" to the values of good selections
//so then with         $resume = explode(";", $resume); I can get the array backj
function set_resume($good_selections) {
    $resume = "";
    foreach($good_selections as $key => $value) {
        $resume .= $key . ":" . $value . ";";
    }
    setcookie("resume", $resume, time() + 3600, "/");
}


//get all the activities
$sql = "SELECT * FROM attivita ORDER BY ora ASC";
$result = $conn->query($sql);
$data = $result->fetch_all(MYSQLI_ASSOC);
$blocchi_analizzati = array();
foreach ($data as $row) {
    $id_blocco = $row['ora'];
    if (!isset($_POST["selezione-blocco-" . $id_blocco])) {
        //echo "Errore di sistema: il blocco orario $id_blocco non &egrave; stata selezionato\n";
        $good_selections[$id_blocco] = 1;
        set_resume($good_selections);
        setcookie("error", "Errore di sistema: il blocco orario $id_blocco non &egrave; stata selezionato", time() + 10, "/");
        header('Location: selezione.php');
        exit();
    }
    //se il blocco orario è già stato analizzato, passa al prossimo
    if (in_array($id_blocco, $blocchi_analizzati)) {
        continue;
    }
    array_push($blocchi_analizzati, $id_blocco);

    $id_attivita_post = $_POST["selezione-blocco-" . $id_blocco];
    $sql = "SELECT * FROM attivita WHERE id = ?";
    $select_stmt = $conn->prepare($sql);
    $select_stmt->bind_param("i", $id_attivita_post);
    $select_stmt->execute();
    $result = $select_stmt->get_result();
    if ($result->num_rows > 0) {
        $attivita = $result->fetch_assoc();
        if ($attivita['ora'] != $id_blocco) {
            //echo "Errore di sistema: il blocco orario $id_blocco non &egrave; stato selezionato correttamente\n";
            $good_selections[$id_blocco] = 1;
            set_resume($good_selections);
            setcookie("error", "Errore di sistema: il blocco orario $id_blocco non &egrave; stato selezionato correttamente", time() + 10, "/");
            header('Location: selezione.php');
            exit();
        }
        if($attivita['posti'] <= 0) {
            //echo "Errore di sistema: l'attivit&agrave; $id_attivita_post non ha pi&ugrave; posti disponibili\n";
            $good_selections[$id_blocco] = 1;
            set_resume($good_selections);
            setcookie("error", "Errore di sistema: l'attivit&agrave; $id_attivita_post non ha pi&ugrave; posti disponibili", time() + 10, "/");
            header('Location: selezione.php');
            exit();
        }
    } else {
        //echo "Errore di sistema: l'attivit&agrave; $id_attivita_post del blocco $id_blocco non &egrave; stata trovata\n";
        $good_selections[$id_blocco] = 1;
        set_resume($good_selections);
        setcookie("error", "Errore di sistema: l'attivit&agrave; $id_attivita_post del blocco $id_blocco non &egrave; stata trovata", time() + 10, "/");
        header('Location: selezione.php');
        exit();
    }
}

//table prenotazioni_attivita: id, id_attivita, id_blocco, id_prenotante, timestamp(automatico)

//ottieni l'id dell'utente a partire dalla sessione
$session_id = $_COOKIE['sessionid'];
$select_stmt = $conn->prepare("SELECT * FROM utenti WHERE sessionid = ?");
$select_stmt->bind_param("s", $session_id);
$select_stmt->execute();
$result = $select_stmt->get_result();
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $id_prenotante = $user['id'];
} else {
    //echo "Errore di sistema: utente non trovato";
    setcookie("error", "Errore di sistema: utente non trovato", time() + 10, "/");
    header('Location: index.php');
    exit();
}

//ora per ogni prenotazione già presente dell'utente, incrementa il numero di posti disponibili dalla tabella attivita dell'attivita corrispondente, poi cancella la prenotazione
$sql = "SELECT * FROM prenotazioni_attivita WHERE id_prenotante = ?";
$select_stmt = $conn->prepare($sql);
$select_stmt->bind_param("i", $id_prenotante);
$select_stmt->execute();
$result = $select_stmt->get_result();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id_attivita = $row['id_attivita'];
        $sql = "UPDATE attivita SET posti = posti + 1 WHERE id = ?";
        $update_stmt = $conn->prepare($sql);
        $update_stmt->bind_param("i", $id_attivita);
        $update_stmt->execute();
    }
    $sql = "DELETE FROM prenotazioni_attivita WHERE id_prenotante = ?";
    $delete_stmt = $conn->prepare($sql);
    $delete_stmt->bind_param("i", $id_prenotante);
    $delete_stmt->execute();
}

//ora per ogni blocco orario, inserisci la prenotazione
foreach ($blocchi_analizzati as $id_blocco) {
    $id_attivita_post = $_POST["selezione-blocco-" . $id_blocco];
    $sql = "INSERT INTO prenotazioni_attivita (id_attivita, id_blocco, id_prenotante) VALUES (?, ?, ?)";
    $insert_stmt = $conn->prepare($sql);
    $insert_stmt->bind_param("iii", $id_attivita_post, $id_blocco, $id_prenotante);
    $insert_stmt->execute();
    //decrementa il numero di posti disponibili
    $sql = "UPDATE attivita SET posti = posti - 1 WHERE id = ?";
    $update_stmt = $conn->prepare($sql);
    $update_stmt->bind_param("i", $id_attivita_post);
    $update_stmt->execute();
}


/**
 * This example shows settings to use when sending via Google's Gmail servers.
 * This uses traditional id & password authentication - look at the gmail_xoauth.phps
 * example to see how to use XOAUTH2.
 * The IMAP section shows how to save this message to the 'Sent Mail' folder using IMAP commands.
 */

//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require 'vendor/autoload.php';

//Create a new PHPMailer instance
$mail = new PHPMailer();

//Tell PHPMailer to use SMTP
$mail->isSMTP();

//Enable SMTP debugging
//SMTP::DEBUG_OFF = off (for production use)
//SMTP::DEBUG_CLIENT = client messages
//SMTP::DEBUG_SERVER = client and server messages
$mail->SMTPDebug = SMTP::DEBUG_SERVER;

//Set the hostname of the mail server
$mail->Host = 'smtp.gmail.com';
//Use `$mail->Host = gethostbyname('smtp.gmail.com');`
//if your network does not support SMTP over IPv6,
//though this may cause issues with TLS

//Set the SMTP port number:
// - 465 for SMTP with implicit TLS, a.k.a. RFC8314 SMTPS or
// - 587 for SMTP+STARTTLS
$mail->Port = 465;

//Set the encryption mechanism to use:
// - SMTPS (implicit TLS on port 465) or
// - STARTTLS (explicit TLS on port 587)
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

//Whether to use SMTP authentication
$mail->SMTPAuth = true;

//Username to use for SMTP authentication - use full email address for gmail
$mail->Username = 'leonapp.noreply@gmail.com';

//Password to use for SMTP authentication
$mail->Password = 'PasswordNonSegreta';

//Set who the message is to be sent from
//Note that with gmail you can only use your account address (same as `Username`)
//or predefined aliases that you have configured within your account.
//Do not use user-submitted addresses in here
$mail->setFrom('leonapp.noreply@gmail.com', 'First Last');

//Set an alternative reply-to address
//This is a good place to put user-submitted addresses
$mail->addReplyTo('niccolo.pagano@studentilicei.leonexiii.it', 'First Last');

//Set who the message is to be sent to
$mail->addAddress('truestop16@gmail.com', 'John Doe');

//Set the subject line
$mail->Subject = 'PHPMailer GMail SMTP test';

//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$mail->msgHTML(file_get_contents('index.php'), __DIR__);

//Replace the plain text body with one created manually
$mail->AltBody = 'This is a plain-text message body';

//Attach an image file
//$mail->addAttachment('images/phpmailer_mini.png');

//send the message, check for errors
if (!$mail->send()) {
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message sent!';
    //Section 2: IMAP
    //Uncomment these to save your message in the 'Sent Mail' folder.
    #if (save_mail($mail)) {
    #    echo "Message saved!";
    #}
}

//Section 2: IMAP
//IMAP commands requires the PHP IMAP Extension, found at: https://php.net/manual/en/imap.setup.php
//Function to call which uses the PHP imap_*() functions to save messages: https://php.net/manual/en/book.imap.php
//You can use imap_getmailboxes($imapStream, '/imap/ssl', '*' ) to get a list of available folders or labels, this can
//be useful if you are trying to get this working on a non-Gmail IMAP server.
function save_mail($mail)
{
    //You can change 'Sent Mail' to any other folder or tag
    $path = '{imap.gmail.com:993/imap/ssl}[Gmail]/Sent Mail';

    //Tell your server to open an IMAP connection using the same username and password as you used for SMTP
    $imapStream = imap_open($path, $mail->Username, $mail->Password);

    $result = imap_append($imapStream, $path, $mail->getSentMIMEMessage());
    imap_close($imapStream);

    return $result;
}