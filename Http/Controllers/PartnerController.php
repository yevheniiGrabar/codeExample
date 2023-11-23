<?php

namespace App\Http\Controllers;

use App\Http\Resources\PartnerResource;
use App\Models\Partner;
use App\Services\GoogleRecaptchaService;
use App\Services\JsonResponseDataTransform;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

/**
 * @group Partner
 *
 * Endpoints for managing partners
 */
class PartnerController extends Controller
{

    /** @var JsonResponseDataTransform */
    public JsonResponseDataTransform $dataTransform;

    /** @var GoogleRecaptchaService */
    public GoogleRecaptchaService $googleRecaptchaService;

    public function __construct(
        JsonResponseDataTransform $dataTransform,
        GoogleRecaptchaService $googleRecaptchaService
    ) {
        $this->dataTransform = $dataTransform;
        $this->googleRecaptchaService = $googleRecaptchaService;
    }

    /**
     * List
     *
     * Returns list of available partners
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->dataTransform->conditionalResponse(
            $request,
            PartnerResource::collection(
                Partner::query()->orderBy('id', 'desc')->get()
            )
        );
    }

    /**
     * Create
     *
     * Store a newly created partner in storage.
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $clientIp = $request->ip();

        $partner = new Partner();

        $partner->company_name = $request->get('$company_name');
        $partner->email = $request->get('email');
        $partner->phone = $request->get('phone');
        $partner->contact_person = $request->get('contact_person');
        $partner->ip_address = $clientIp;
        $partner->save();

        return new JsonResponse(new PartnerResource($partner), Response::HTTP_CREATED);
    }

    /**
     * Show
     *
     * Display the specified partner.
     * @authenticated
     * @param Partner $partner
     * @return JsonResponse
     */
    public function show(Partner $partner): JsonResponse
    {
        return new JsonResponse(PartnerResource::make($partner));
    }


    /**
     * Edit
     *
     * Update the specified partner in storage.
     * @authenticated
     * @param Request $request
     * @param Partner $partner
     * @return JsonResponse
     */
    public function update(Request $request, Partner $partner): JsonResponse
    {
        $partner->update($request->all());

        return new JsonResponse(PartnerResource::make($partner), Response::HTTP_CREATED);
    }

    /**
     * Delete
     *
     * Remove the specified partner from storage.
     * @authenticated
     * @param Partner $partner
     * @return JsonResponse
     */
    public function destroy(Partner $partner): JsonResponse
    {
        $partner->delete();

        return new JsonResponse(['message' => 'Partner deleted successfully'], Response::HTTP_NO_CONTENT);
    }

    /**
     * Send email with company information
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendEmail(Request $request): JsonResponse
    {
        $data = [
            'company_name' => $request->get('company_name'),
            'email' => $request->get('email'),
            'phone' => $request->get('phone'),
            'contactPerson' => $request->get('contact_person'),
        ];

        if (!empty($request->get('response'))) {
            $verifyRecaptcha = $this->googleRecaptchaService->verifyRecaptcha($request);

            if ($verifyRecaptcha->getStatusCode() == 422) {
                return new JsonResponse(['payload' => $verifyRecaptcha->getData()], $verifyRecaptcha->getStatusCode());
            } else {
                Mail::send(
                    'emails.company_info',
                    $data,
                    function ($message) {
                        $message->to(config('email.partners.' . config('app.env')))
                            ->subject('Information about the new partner');
                    }
                );
            }
        }

        return new JsonResponse(['payload' => $verifyRecaptcha->getData()], $verifyRecaptcha->getStatusCode());
    }

}
