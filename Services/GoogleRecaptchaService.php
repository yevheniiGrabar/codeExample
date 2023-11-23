<?php


namespace App\Services;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GoogleRecaptchaService
{
    /**
     * Checking reCAPTCHA and sending a request to an external API.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyRecaptcha(Request $request): JsonResponse
    {
        $secretKey = config('services.recaptcha.secret_key');
        $recaptchaResponse = $request->input('response');

        $response = Http::post(
            'https://www.google.com/recaptcha/api/siteverify?secret=' . $secretKey . '&response=' . $recaptchaResponse
        );

        $responseData = $response->json();

        if ($responseData['success']) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false], 422);
        }
    }
}
