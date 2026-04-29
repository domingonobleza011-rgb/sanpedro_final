if (isset($_POST['g-recaptcha-response'])) {
    $secret = '6Ldeb9AsAAAAAEKAkvmbBIc91beRnzabAJJSbgSK';
    $response = $_POST['g-recaptcha-response'];
    $remoteIp = $_SERVER['REMOTE_ADDR'];

    // Call Google's API to verify
    $verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$response&remoteip=$remoteIp");
    $responseData = json_decode($verifyResponse);

    if ($responseData->success) {
        // Human confirmed! Proceed with your database logic
        echo "Success! Processing your request...";
    } else {
        // Failed verification
        echo "Please complete the CAPTCHA correctly.";
    }
} else {
    echo "CAPTCHA not found. Please try again.";
}