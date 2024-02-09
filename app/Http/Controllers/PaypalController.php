<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PayPalController extends Controller
{
    private $base = 'https://api.paypal.com';

    /**
     * Generate an OAuth 2.0 access token for authenticating with PayPal REST APIs.
     * @see https://developer.paypal.com/api/rest/authentication/
     */
    private function generateAccessToken()
    {
        try {
            $clientId = config('services.paypal.client_id');
            $clientSecret = config('services.paypal.client_secret');

            if (!$clientId || !$clientSecret) {
                throw new \Exception('MISSING_API_CREDENTIALS');
            }

            $auth = base64_encode("$clientId:$clientSecret");
            $response = Http::withHeaders([
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => "Basic $auth",
            ])->asForm()
            ->post("{$this->base}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

            Log::info($response);
            $data = $response->json();
            Log::info($data);
            return $data['access_token'];
        } catch (\Exception $error) {
            \Log::error('Failed to generate Access Token: ' . $error->getMessage());
        }
    }

    /**
     * Create an order to start the transaction.
     * @see https://developer.paypal.com/docs/api/orders/v2/#orders_create
     */
    public function createOrder(Request $request)
    {  
        try {
            $cart = $request->input('cart');

            // Use $cart to calculate the purchase unit details

            $accessToken = $this->generateAccessToken();
            $url = "{$this->base}/v2/checkout/orders";
            $payload = [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => '100.00',
                    ]],
                ],
            ];
            $jsonPayload = json_encode($payload);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer $accessToken",
            ])->post($url, $payload);
            Log::info($response);
            return $this->handleResponse($response);
        } catch (\Exception $error) {
            \Log::error('Failed to create order: ' . $error->getMessage());
            return response()->json(['error' => 'Failed to create order.'], 500);
        }
    }

    /**
     * Capture payment for the created order to complete the transaction.
     * @see https://developer.paypal.com/docs/api/orders/v2/#orders_capture
     */
    public function captureOrder($orderID)
    {
        try {
            $accessToken = $this->generateAccessToken();
            $url = "{$this->base}/v2/checkout/orders/{$orderID}/capture";

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer $accessToken",
            ])->post($url);

            return $this->handleResponse($response);
        } catch (\Exception $error) {
            \Log::error('Failed to capture order: ' . $error->getMessage());
            return response()->json(['error' => 'Failed to capture order.'], 500);
        }
    }

    private function handleResponse($response)
    {
        try {
            $jsonResponse = $response->json();
            return response()->json([
                'jsonResponse' => $jsonResponse,
                'httpStatusCode' => $response->status(),
            ]);
        } catch (\Exception $error) {
            $errorMessage = $response->body();
            throw new \Exception($errorMessage);
        }
    }
}
