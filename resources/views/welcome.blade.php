<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayPal JS SDK Standard Integration</title>
  </head>
  <body>
    <div id="paypal-button-container"></div>
    <p id="result-message"></p>
    <!-- Replace the "test" client-id value with your client-id -->
    <script src="https://www.paypal.com/sdk/js?client-id=AVnwf7qg_UQV8tXGqkowoloX39vi50OU4CHiaIo6b43rwc8CgkfOWiymICQbnLQMbKOPziSnlxhHI95S&components=buttons&enable-funding=paylater,venmo,card" data-sdk-integration-source="integrationbuilder_sc"></script>
    <script src="{{asset('js/app.js')}}"></script>
  </body>
</html>
