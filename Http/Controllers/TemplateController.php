<?php

namespace App\Http\Controllers;

use App\Http\Resources\TemplateResource;
use App\Models\Template;
use App\Services\JsonResponseDataTransform;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

/**
 * @group Template
 *
 * Endpoints for managing templates
 */
class TemplateController extends Controller
{
    /**
     * @var JsonResponseDataTransform
     */
    public JsonResponseDataTransform $dataTransform;

    public function __construct(JsonResponseDataTransform $dataTransform)
    {
        $this->dataTransform = $dataTransform;
    }

    /**
     * List
     *
     * Returns list of available templates
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->dataTransform->conditionalResponse($request, TemplateResource::collection(Template::all()));
    }

    /**
     * Create
     *
     * Store a newly created template in storage.
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $template = Template::query()->create(
            [
                'name' => $request->get('name'),
                'disabled_fields' => json_encode($request->get('disabled_fields')),
            ]
        );

        return new JsonResponse(['payload' => new TemplateResource($template)]);
    }

    /**
     * Show
     *
     * Display the specified template.
     * @authenticated
     * @param Template $template
     * @return JsonResponse
     */
    public function show(Template $template): JsonResponse
    {
        return new JsonResponse(['payload' => TemplateResource::make($template)]);
    }

    /**
     * Edit
     *
     * Update the specified template in storage.
     * @authenticated
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $this->validate(
            $request,
            [
                'name' => 'string', // Поле "name" должно быть строкой
//                'disabled_fields' => 'json' // Поле "disabled_fields" должно быть в формате JSON
            ]
        );

        try {
            $template = Template::query()->findOrFail($id);

            if ($request->has('name')) {
                $template->name = $request->input('name');
            }

            // Обновить поле "disabled_fields", если оно передано
            if ($request->has('disabled_fields')) {
                $template->disabled_fields = json_encode($request->input('disabled_fields'));
            }

            $template->save();

            return new JsonResponse(['payload' => new TemplateResource($template)]);
        } catch (\Exception $e) {
            return new JsonResponse(
                ['payload' => 'Failed to update Template: ' . $e->getMessage()],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    /**
     * Delete
     *
     * Remove the specified template from storage.
     * @authenticated
     * @param Template $template
     * @return JsonResponse
     */
    public function destroy(Template $template): JsonResponse
    {
        $template->delete();

        return new JsonResponse([]);
    }
}
