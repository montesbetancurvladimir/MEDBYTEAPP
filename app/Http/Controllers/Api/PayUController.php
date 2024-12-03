<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PayUService;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayUController extends Controller
{
    protected $payUService;

    public function __construct(PayUService $payUService){
        $this->payUService = $payUService;
    }

    // Método para crear un token
    public function createToken(Request $request){
        try {
            // Validar los datos de entrada
            $validatedData = $request->validate([
                'payerId' => 'required|string',
                'name' => 'required|string',
                'identificationNumber' => 'required|string',
                'paymentMethod' => 'required|string',
                'number' => 'required|string',
                'expirationDate' => 'required|string|regex:/\d{4}\/\d{2}/',
            ]);

            // Crear el token usando el servicio PayU
            $response = $this->payUService->createToken($validatedData);

            // Verificar si la respuesta contiene errores
            if (isset($response['code']) && $response['code'] !== 'SUCCESS') {
                Log::error('Error al crear el token', ['response' => $response]);
                return response()->json(['error' => 'Error al crear el token', 'details' => $response], 400);
            }

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Excepción al crear el token', ['exception' => $e->getMessage()]);
            return response()->json(['error' => 'Ocurrió un error inesperado al crear el token. Inténtelo de nuevo más tarde.'], 500);
        }
    }

    //Detalles del token, para mandarlo en el proceso del pago
    public function getTokenDetails(Request $request){
        // Validar los datos de entrada
        $validatedData = $request->validate([
            'creditCardTokenId' => 'required|string',
        ]);

        try {
            // Configurar la solicitud a la API de PayU
            $response = Http::post('https://sandbox.api.payulatam.com/payments-api/4.0/service.cgi', [
                'language' => 'es',
                'command' => 'GET_TOKEN',
                'merchant' => [
                    'apiLogin' => env('PAYU_API_LOGIN'),
                    'apiKey' => env('PAYU_API_KEY'),
                ],
                'creditCardToken' => [
                    'creditCardTokenId' => $validatedData['creditCardTokenId']
                ]
            ]);

            // Obtener los datos de la respuesta
            $responseData = $response->json();

            // Verificar si la respuesta indica éxito
            if (isset($responseData['code']) && $responseData['code'] !== 'SUCCESS') {
                return response()->json(['error' => 'Error al consultar el token', 'details' => $responseData], 400);
            }

            // Retornar la respuesta de la API
            return response()->json($responseData);
        } catch (\Exception $e) {
            Log::error('Excepción al consultar el token', ['exception' => $e->getMessage()]);
            return response()->json(['error' => 'Ocurrió un error inesperado al consultar el token. Inténtelo de nuevo más tarde.'], 500);
        }
    }

    // Método para validar un token
    public function validateToken(Request $request){
        // Validar los datos de entrada
        $validatedData = $request->validate([
            'creditCardTokenId' => 'required|string',
        ]);

        try {
            // Enviar la solicitud de validación de token a PayU
            $response = Http::post('https://sandbox.api.payulatam.com/payments-api/4.0/service.cgi', [
                "language" => "es",
                "command" => "VALIDATE_TOKEN",
                "merchant" => [
                    "apiLogin" => env('PAYU_API_LOGIN'),
                    "apiKey" => env('PAYU_API_KEY')
                ],
                "creditCardToken" => [
                    "creditCardTokenId" => $validatedData['creditCardTokenId']
                ]
            ]);

            // Obtener los datos de la respuesta
            $responseData = $response->json();
            
            // Verificar si la validación fue exitosa
            if (isset($responseData['code']) && $responseData['code'] !== 'SUCCESS') {
                return response()->json(['error' => 'Token inválido o error en la validación', 'details' => $responseData], 400);
            }

            // Retornar la respuesta de la API
            return response()->json($responseData);
        } catch (\Exception $e) {
            // Manejo de excepciones
            return response()->json(['error' => 'Ocurrió un error inesperado al validar el token. Inténtelo de nuevo más tarde.'], 500);
        }
    }

    // Método para realizar un pago utilizando un token
    public function makePayment(Request $request){
        try {
            // Validar los datos de entrada
            $validatedData = $request->validate([
                'referenceCode' => 'required|string',
                'description' => 'required|string',
                'value' => 'required|numeric',
                'currency' => 'required|string',
                'buyerId' => 'required|string',
                'buyerName' => 'required|string',
                'buyerEmail' => 'required|string|email',
                'buyerPhone' => 'required|string',
                'buyerDni' => 'required|string',
                'buyerStreet' => 'required|string',
                'buyerCity' => 'required|string',
                'buyerState' => 'required|string',
                'buyerCountry' => 'required|string',
                'buyerPostalCode' => 'required|string',
                'payerId' => 'required|string',
                'payerName' => 'required|string',
                'payerEmail' => 'required|string|email',
                'payerPhone' => 'required|string',
                'payerDni' => 'required|string',
                'payerStreet' => 'required|string',
                'payerCity' => 'required|string',
                'payerState' => 'required|string',
                'payerCountry' => 'required|string',
                'payerPostalCode' => 'required|string',
                'creditCardTokenId' => 'required|string',
                'paymentMethod' => 'required|string',
                'paymentCountry' => 'required|string',
                'deviceSessionId' => 'required|string',
                'ipAddress' => 'required|string',
                'cookie' => 'required|string',
                'userAgent' => 'required|string',
            ]);

            // Enviar la solicitud de pago a PayU
            $response = Http::post('https://sandbox.api.payulatam.com/payments-api/4.0/service.cgi', [
                "language" => "es",
                "command" => "SUBMIT_TRANSACTION",
                "merchant" => [
                    "apiLogin" => env('PAYU_API_LOGIN'),
                    "apiKey" => env('PAYU_API_KEY')
                ],
                "transaction" => [
                    "order" => [
                        "accountId" => env('PAYU_ACCOUNT_ID'),
                        "referenceCode" => $validatedData['referenceCode'],
                        "description" => $validatedData['description'],
                        "language" => "es",
                        "signature" => hash('md5', env('PAYU_API_KEY') . "~" . env('PAYU_MERCHANT_ID') . "~" . $validatedData['referenceCode'] . "~" . $validatedData['value'] . "~" . $validatedData['currency']),
                        "notifyUrl" => $request->notifyUrl,
                        "additionalValues" => [
                            "TX_VALUE" => [
                                "value" => $validatedData['value'],
                                "currency" => $validatedData['currency']
                            ]
                        ],
                        "buyer" => [
                            "merchantBuyerId" => $validatedData['buyerId'],
                            "fullName" => $validatedData['buyerName'],
                            "emailAddress" => $validatedData['buyerEmail'],
                            "contactPhone" => $validatedData['buyerPhone'],
                            "dniNumber" => $validatedData['buyerDni'],
                            "shippingAddress" => [
                                "street1" => $validatedData['buyerStreet'],
                                "city" => $validatedData['buyerCity'],
                                "state" => $validatedData['buyerState'],
                                "country" => $validatedData['buyerCountry'],
                                "postalCode" => $validatedData['buyerPostalCode'],
                                "phone" => $validatedData['buyerPhone']
                            ]
                        ]
                    ],
                    "payer" => [
                        "merchantPayerId" => $validatedData['payerId'],
                        "fullName" => $validatedData['payerName'],
                        "emailAddress" => $validatedData['payerEmail'],
                        "contactPhone" => $validatedData['payerPhone'],
                        "dniNumber" => $validatedData['payerDni'],
                        "billingAddress" => [
                            "street1" => $validatedData['payerStreet'],
                            "city" => $validatedData['payerCity'],
                            "state" => $validatedData['payerState'],
                            "country" => $validatedData['payerCountry'],
                            "postalCode" => $validatedData['payerPostalCode'],
                            "phone" => $validatedData['payerPhone']
                        ]
                    ],
                    "creditCardTokenId" => $validatedData['creditCardTokenId'],
                    "paymentMethod" => $validatedData['paymentMethod'],
                    "paymentCountry" => $validatedData['paymentCountry'],
                    "deviceSessionId" => $validatedData['deviceSessionId'],
                    "ipAddress" => $validatedData['ipAddress'],
                    "cookie" => $validatedData['cookie'],
                    "userAgent" => $validatedData['userAgent'],
                    "type" => "AUTHORIZATION_AND_CAPTURE",
                    "test" => true
                ]
            ]);

            $responseData = $response->json();

            // Verificar si la respuesta contiene errores
            if (isset($responseData['code']) && $responseData['code'] !== 'SUCCESS') {
                Log::error('Error al procesar el pago', ['response' => $responseData]);
                return response()->json(['error' => 'Error al procesar el pago', 'details' => $responseData], 400);
            }

            // Guardar los detalles de la transacción en la base de datos
            $transaction = new Transaction();
            $transaction->reference_code = $validatedData['referenceCode'];
            $transaction->description = $validatedData['description'];
            $transaction->value = $validatedData['value'];
            $transaction->currency = $validatedData['currency'];
            $transaction->payment_method = $validatedData['paymentMethod'];
            $transaction->transaction_id = $responseData['transactionResponse']['transactionId'] ?? null;
            $transaction->transaction_status = $responseData['transactionResponse']['state'] ?? null;
            $transaction->payer_id = $validatedData['payerId'];
            $transaction->buyer_id = $validatedData['buyerId'];
            $transaction->save();

            return response()->json($responseData);
        } catch (\Exception $e) {
            Log::error('Excepción al procesar el pago', ['exception' => $e->getMessage()]);
            return response()->json(['error' => 'Ocurrió un error inesperado al procesar el pago. Inténtelo de nuevo más tarde.'], 500);
        }
    }

    // Método para consultar el estado de una transacción
    public function getTransactionStatus(Request $request){
        try {
            $validatedData = $request->validate([
                'transactionId' => 'required|string',
            ]);

            $response = Http::get('https://sandbox.api.payulatam.com/payments-api/4.0/service.cgi', [
                "language" => "es",
                "command" => "GET_TRANSACTION_DETAILS",
                "merchant" => [
                    "apiLogin" => env('PAYU_API_LOGIN'),
                    "apiKey" => env('PAYU_API_KEY')
                ],
                "transaction" => [
                    "transactionId" => $validatedData['transactionId']
                ]
            ]);

            $responseData = $response->json();
            if (isset($responseData['code']) && $responseData['code'] !== 'SUCCESS') {
                Log::error('Error al consultar el estado de la transacción', ['response' => $responseData]);
                return response()->json(['error' => 'Error al consultar el estado de la transacción', 'details' => $responseData], 400);
            }

            return response()->json($responseData);
        } catch (\Exception $e) {
            Log::error('Excepción al consultar el estado de la transacción', ['exception' => $e->getMessage()]);
            return response()->json(['error' => 'Ocurrió un error inesperado al consultar el estado de la transacción. Inténtelo de nuevo más tarde.'], 500);
        }
    }

    // Método para procesar un reembolso (falta hacer el registro en la base de datos)
    public function refundPayment(Request $request){
        try {
            $validatedData = $request->validate([
                'transactionId' => 'required|string',
                'amount' => 'required|numeric',
                'currency' => 'required|string',
                'reason' => 'required|string',
            ]);

            $response = Http::post('https://sandbox.api.payulatam.com/payments-api/4.0/service.cgi', [
                "language" => "es",
                "command" => "SUBMIT_REFUND",
                "merchant" => [
                    "apiLogin" => env('PAYU_API_LOGIN'),
                    "apiKey" => env('PAYU_API_KEY')
                ],
                "transaction" => [
                    "transactionId" => $validatedData['transactionId'],
                    "amount" => [
                        "value" => $validatedData['amount'],
                        "currency" => $validatedData['currency']
                    ],
                    "reason" => $validatedData['reason']
                ]
            ]);

            $responseData = $response->json();
            if (isset($responseData['code']) && $responseData['code'] !== 'SUCCESS') {
                Log::error('Error al procesar el reembolso', ['response' => $responseData]);
                return response()->json(['error' => 'Error al procesar el reembolso', 'details' => $responseData], 400);
            }

            return response()->json($responseData);
        } catch (\Exception $e) {
            Log::error('Excepción al procesar el reembolso', ['exception' => $e->getMessage()]);
            return response()->json(['error' => 'Ocurrió un error inesperado al procesar el reembolso. Inténtelo de nuevo más tarde.'], 500);
        }
    }

    // Método para manejar notificaciones de webhook (según el estado toca realizar la lógica)
    public function handleWebhook(Request $request){
        try {
            // Validar los datos del webhook
            $data = $request->all();

            // Procesar la notificación (e.g., actualizar el estado de la transacción)
            // Implementar lógica específica para tu caso

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Excepción al manejar el webhook', ['exception' => $e->getMessage()]);
            return response()->json(['error' => 'Ocurrió un error inesperado al manejar la notificación.'], 500);
        }
    }

}
