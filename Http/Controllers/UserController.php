<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\DestroyRequest;
use App\Http\Requests\User\IndexRequest;
use App\Http\Requests\User\InviteRequest;
use App\Http\Requests\User\ShowRequest;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\MeResource;
use App\Http\Resources\UserResource;
use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\Role;
use App\Models\User;
use App\Services\JsonResponseDataTransform;
use App\Services\UserService;
use App\Support\Ability\AbilityResolver;
use App\Traits\CurrentCompany;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * @group User
 *
 * Endpoints for managing users
 */
class UserController extends Controller
{
    use CurrentCompany;

    /** @var JsonResponseDataTransform */
    public JsonResponseDataTransform $dataTransform;

    protected UserService $userService;

    public function __construct(JsonResponseDataTransform $dataTransform, UserService $userService)
    {
        $this->dataTransform = $dataTransform;
        $this->userService = $userService;
    }

    /**
     * List
     *
     * Returns list of available users
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        $users = [];
        $companyUsers = CompanyUser::query()->where('company_id', $currentCompany->company_id)->get();
        foreach ($companyUsers as $companyUser) {
            $user = User::query()->find($companyUser->user_id);

            if ($user) {
                $users[] = $user;
            }
        }

        //        $this->authorize(Access::VIEW_LIST_USERS, User::class);
        return $this->dataTransform->conditionalResponse($request, UserResource::setMode('single')::collection($users));
    }

    /**
     * Create
     *
     * Store a newly created user in storage.
     * @authenticated
     * @param StoreRequest $request
     * @return JsonResponse
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $pivotData = $this->getDefaultCompany();

        $user = User::query()->create($request->validated());
        $user->companies()->attach($pivotData->company_id);

        return new JsonResponse(new UserResource($user), Response::HTTP_CREATED);
    }

    /**
     * Show
     *
     * Display the specified user.
     * @authenticated
     * @param User $user
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(ShowRequest $request, User $user): JsonResponse
    {
        return new JsonResponse(
            MeResource::make($user)->additional(
                array_merge(
                    [
                        'roles' => $user->role()->get(),
                        'permissions' => AbilityResolver::groupAbilities($user->role),
                    ]
                )
            )
        );
    }

    /**
     * Show user companies
     *
     * Display authenticated user`s list of companies.
     * @authenticated
     * @return JsonResponse
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function userCompany(): JsonResponse
    {
        if (!auth()->user()) {
            return new JsonResponse(["message" => "Not authenticated"], 403, [], JSON_PRETTY_PRINT);
        }
        $company = auth()->user()->companies()->wherePivot('user_id', '=', auth()->id())->get();

        return new JsonResponse(CompanyResource::collection($company)->response()->getData(true));
    }

    /**
     * Show default company
     *
     * Display default authenticated user company.
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */

    public function defaultUserCompany(Request $request): JsonResponse
    {
        if (!auth()->user()) {
            return new JsonResponse(["message" => "Not authenticated"], 403, [], JSON_PRETTY_PRINT);
        }
        $company = auth()->user()->companies()->wherePivot('is_default', '=', '1')->get();

        return $this->dataTransform->conditionalResponse($request, CompanyResource::collection($company));
    }

    /**
     * Edit default company
     *
     * Update default authenticated user company
     * @authenticated
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function setDefaultUserCompany(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();
        $company = Company::find($id);

        if (!$company) {
            return new JsonResponse(['error' => 'Компания не найдена'], 404);
        }

        // Обновление всех компаний пользователя и установка 'is_default' в 0
        $user->companies()->update(['is_default' => 0]);

        // Установка выбранной компании как 'is_default'
        $user->companies()->updateExistingPivot($company->id, ['is_default' => 1]);

        // Обновление модели компании после изменений
        $company->refresh();

        return new JsonResponse(['payload' => $company]);
    }

    /**
     * Edit
     *
     * Update the specified user in storage.
     * @authenticated
     * @param User $user
     * @param UpdateRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     *
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function update(UpdateRequest $request, User $user): JsonResponse
    {
        $user = $this->userService->updateUser($user, $request->validated());

        return new JsonResponse(new UserResource($user));
    }

    /**
     * Delete
     *
     * Remove the specified user from storage.
     * @authenticated
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(DestroyRequest $request, User $user): JsonResponse
    {
        $user->companies()->detach();
        $user->delete();

        return new JsonResponse(['message' => 'User deleted successfully'], Response::HTTP_OK);
    }

    /**
     * Edit password
     *
     * Update the authenticated user`s password.
     * @authenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $user = Auth::user();
        $currentPassword = $request->get('current_password');
        $newPassword = $request->get('new_password');
        $confirmPassword = $request->get('confirm_password');

        if (!Hash::check($currentPassword, $user->password)) {
            return response()->json(['message' => 'The current password is incorrect.'], Response::HTTP_BAD_REQUEST);
        }

        // Check if the new password and the confirmation password match
        if ($newPassword !== $confirmPassword) {
            return response()->json(['message' => 'The new password and the confirmation password do not match.'], 400);
        }

        // Update the user's password
        $user->update(
            [
                'password' => Hash::make($newPassword)
            ]
        );

        return new JsonResponse([
            'payload' => ['message' => 'Password updated successfully.']
        ]);
    }

    /**
     * Invite user.
     *
     * Inviting new users to the platform.
     * @param InviteRequest $request
     * @return JsonResponse
     */
    public function inviteUser(InviteRequest $request): JsonResponse
    {
        $user = $this->userService->inviteUser($request->validated());

        return new JsonResponse(
            [
                'payload' => [
                    'message' => 'Successfully invited user.',
                    'data' => new UserResource($user)
                ]
            ],
            Response::HTTP_CREATED
        );
    }
}
