<?php
 header("Access-Control-Allow-Origin: *");
?>
<html>

<head>
  <meta name="google-signin-client_id"
    content="626536855617-es6i5nct122k3tbci1qghpfj21k5mavf.apps.googleusercontent.com">
</head>

<body>
  <div id="my-signin2"></div>
  <script>
    // Render the Google Sign-In button
    function renderButton() {
      gapi.signin2.render('my-signin2', {
      'scope': 'profile email',
      'width': 240,
      'height': 50,
      'longtitle': true,
      'theme': 'dark',
      'onsuccess': onSuccess,
      'onfailure': onFailure
    });
    }
    
    // Sign-in success callback
    function onSuccess(googleUser) {
      // Get the user's ID token and basic profile information
      var id_token = googleUser.getAuthResponse().id_token;
      var profile = googleUser.getBasicProfile();

      // Send the ID token and email to your server for validation
      // and to create or update the user's account
      var email = profile.getEmail();
      var email_dom = email.split("@")[1];
      if (email_dom == "studentilicei.leonexiii.it") {
        // redirect to the destination
      } else {
        alert("Email non valida")
      }
    }
    // Sign-in failure callback
    function onFailure(error) {
      console.log(error);
    }
  </script>

  <script src="https://apis.google.com/js/platform.js?onload=renderButton" async defer></script>
</body>

</html>