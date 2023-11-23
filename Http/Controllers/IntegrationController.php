<?php

namespace App\Http\Controllers;

use App\Http\Requests\Integration\IntegrateRequest;
use App\Http\Resources\IntegrationResource;
use App\Models\Integration;
use App\Models\PowerOfficeAuth;
use App\Models\ShipmondoAuth;
use App\Support\PowerOffice\PowerOffice;
use App\Support\Shipmondo\Shipmondo;
use App\Traits\CurrentCompany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @group Integration
 *
 * Endpoints for managing integrations
 */
class IntegrationController extends Controller
{
    /**
     * List
     *
     * Returns list of available integrations
     * @authenticated
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $companyShipmondoAuth = ShipmondoAuth::where('company_id', CurrentCompany::getDefaultCompany()->company_id)
            ->first();
        $companyPowerOffice = PowerOfficeAuth::where('company_id', CurrentCompany::getDefaultCompany()->company_id)
            ->first();

        return response()->json([
            'payload' => [
                [
                    'id' => 1,
                    'integrated' => (bool) $companyPowerOffice,
                    'name' => 'PowerOffice GO',
                    'slug' => 'poweroffice',
                    'imageUrl' => config('app.url') . '/images/poweroffice.png',
                    'description' => 'PowerOffice GO is one of Norway\'s most popular and user-friendly accounting systems for small and medium-sized businesses.',
                    'integrationType' => 'accounting',
                    'modal' => [
                        'fields' => [
                            [
                                'name' => 'auth_string_one',
                                'label' => 'Enter your PowerOffice GO api-key',
                                'placeholder' => '',
                                'required' => true,
                            ],
                            [
                                'name' => 'auth_string_two',
                                'label' => 'Enter your PowerOffice GO client-key',
                                'placeholder' => '',
                                'required' => true,
                            ]
                        ]
                    ],
                ],
                [
                    'id' => 2,
                    'integrated' => (bool) $companyShipmondoAuth,
                    'name' => 'Shipmondo',
                    'slug' => 'shipmondo',
                    'imageUrl' => config('app.url') . '/images/shipmondo.png',
                    'description' => 'Ship across carriers from one platform. Shipmondo lifts your logistics to the next level. Easy freight and order management. No subscriptions.',
                    'integrationType' => 'shipping',
                    'modal' => [
                        'fields' => [
                            [
                                'name' => 'auth_string_one',
                                'label' => 'Enter your Shipmondo user',
                                'placeholder' => '',
                                'required' => true,
                            ],
                            [
                                'name' => 'auth_string_two',
                                'label' => 'Enter your Shipmondo key',
                                'placeholder' => '',
                                'required' => true,
                            ]
                        ]
                    ],
                ],
            ]
        ]);
    }

    /**
     * Integrate
     *
     * Integrate current company with selected service
     * @authenticated
     * @return JsonResponse
     */
    public function integrate(IntegrateRequest $request, string $integration): JsonResponse
    {
        $currentCompanyId = CurrentCompany::getDefaultCompany()->company_id;

        if($integration === 'shipmondo') {
            $this->integrateShipmondo($currentCompanyId, $request);
        }else if($integration === 'poweroffice') {
            $this->integratePowerOffice($currentCompanyId, $request);
        }else{
            response()->json(['payload' => false], 400);
        }

        return response()->json([
            'payload' => true
        ]);
    }

    private function integrateShipmondo($currentCompanyId, $request): void
    {
        new Shipmondo($request->input('auth_string_one'), $request->input('auth_string_two'));

        $shipmondoAuth = ShipmondoAuth::where('company_id', $currentCompanyId)->first();

        if($shipmondoAuth) {
            $shipmondoAuth->update([
                'user' => $request->input('auth_string_one'),
                'key' => $request->input('auth_string_two'),
            ]);
        }else{
            ShipmondoAuth::create([
                'company_id' => $currentCompanyId,
                'user' => $request->input('auth_string_one'),
                'key' => $request->input('auth_string_two'),
            ]);
        }
    }

    private function integratePowerOffice($currentCompanyId, $request): void
    {
        (new PowerOffice())
            ->testConnection($request->input('auth_string_one'), $request->input('auth_string_two'));

        $powerOfficeAuth = PowerOfficeAuth::where('company_id', $currentCompanyId)->first();

        if($powerOfficeAuth) {
            $powerOfficeAuth->update([
                'app_key' => $request->input('auth_string_one'),
                'client_key' => $request->input('auth_string_two'),
            ]);
        }else{
            PowerOfficeAuth::create([
                'company_id' => $currentCompanyId,
                'app_key' => $request->input('auth_string_one'),
                'client_key' => $request->input('auth_string_two'),
            ]);
        }
    }
}
