<?php
    $config = include '../config.php';
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Contact form demo</title>
    <meta name="author" content="Gustavo Rodríguez" />
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.2/dist/jquery.validate.min.js"></script>

    <script src="https://www.google.com/recaptcha/api.js?render=<? echo $config['recaptchaKey']; ?>"></script>
  </head>
  <body>
    <h1>Contact me!</h1>
    <form id="contact">
        <div>
            <label for="contact-email">Email</label>
            <input id="contact-email" type="email" name="email" placeholder="Enter your email" />
        </div>
        
        <div>
            <label for="contact-message">Message</label>
            <textarea id="contact-message" name="message" placeholder="Enter your message"></textarea>
        </div>
        
        <div>
            <input type="submit" value="Send" />
            <span id="contact-result"></span>
        </div>
    </form>

    <style>
        .error {
            color: red;
            display: block
        }
        .success {
            color: green;
            display: block
        }
    </style>

    <script>
        let RECAPTCHA_PUBLIC_KEY = '<? echo $config['recaptchaKey']; ?>';

        // On document ready
        $(document).ready(() => {
            // Call validate method from jquery validate plugin
            $('#contact').validate({
                rules: {
                    email: {
                        required: true,
                        email: true
                    },
                    message: {
                        required: true
                    }
                },
                submitHandler: function(form) {
                    //Get recatpcha token
                    getRecaptchaToken(function(recaptchaToken){
                        // Create form data from form element
                        let fd = new FormData($(form)[0]);

                        //Add recaptcha key
                        fd.append('recaptchaToken', recaptchaToken);

                        // Clear message
                        hideResult();

                        // Do ajax call
                        $.ajax({
                            url: '/contact.php',
                            type: 'POST',
                            data: fd,
                            async: true,
                            contentType: false,
                            processData: false
                        }).done(function(response) {
                            // Parse response as JSON
                            const json = JSON.parse(response);

                            //Check result
                            if(json.success){
                                displayResult(true, 'Message was sent :)');

                                //Clear form
                                $('#contact').trigger('reset');

                                //Wait 3 seconds to clear success result
                                setTimeout(hideResult, 3000);
                                return;
                            }

                            // Display error
                            displayResult(false, json.error ? json.error : 'Something went wrong :(');
                        }).fail(function(e) {
                            // Load error message
                            displayResult(false, 'Something went wrong :(');
                        });
                    });
                    return false;
                }
            });
        });

        function displayResult(status, message){
            const $result = $('#contact-result');

            // Add status classes
            $result.addClass(status ? 'success' : 'error');

            // Load text
            $result.text(message);

            //Show it
            $result.show();
        }

        function hideResult(){
            const $result = $('#contact-result');

            // Remove status classes
            $result.removeClass('error');
            $result.removeClass('success');

            // Clear text
            $result.text('');

            //Show it
            $result.hide();
        }

        function getRecaptchaToken(callback) {
            grecaptcha.ready(function() {
                grecaptcha.execute(RECAPTCHA_PUBLIC_KEY).then(callback);
            });
        }
    </script>
  </body>
</html>