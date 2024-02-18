## About 

Integration of PayPal API into a Laravel Project. Clone this project and modify according to your needs. 

## What you'll need to do

Set up .env file and add client id and secret into the following variables: 
- PAYPAL_CLIENT_ID
- PAYPAL_CLIENT_SECRET

At the welcome blade view, look for this script tag:
script src="https://www.paypal.com/sdk/js?client-id=test&components=buttons&enable-funding=paylater,venmo,card" data-sdk-integration-source="integrationbuilder_sc"></script>
and replace test with actual client ID. 
