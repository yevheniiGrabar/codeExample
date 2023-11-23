<?php

namespace App\Http\Controllers;

use App\Http\Requests\Company\StoreRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\MeResource;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use App\Notifications\PasswordChangedNotification;
use App\Notifications\WelcomeNotification;
use App\Support\Ability\AbilityResolver;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @group Authentication
 *
 * Endpoints for authentication purpose
 */
class AuthController extends Controller
{
    /**
     * Registration
     *
     * Create a new User
     * @param  RegisterRequest  $request
     * @param  StoreRequest  $companyStoreRequest
     * @return JsonResponse
     */
    public function register(RegisterRequest $request, StoreRequest $companyStoreRequest): JsonResponse
    {
        $validatedUser = $request->validated();
        $validatedCompany = $companyStoreRequest->validated();

        $user = User::query()->create(
            [
                'name' => $validatedUser['first_name'],
                'last_name' => $validatedUser['last_name'] ?? '',
                'email' => $validatedUser['email'],
                'password' => bcrypt($validatedUser['password']),
            ]
        );

        $newCompany = Company::query()->create(
            [
                'company_name' => $validatedCompany['company_name'],
                'industry_id' => $validatedCompany['industry'] ?? null,
                'country_id' => $validatedCompany['country'] ?? null,
                'street' => $validatedCompany['street'],
                'street_2' => $validatedCompany['street_2'] ?? null,
                'city' => $validatedCompany['city'],
                'zipcode' => $validatedCompany['zipcode'],
                'phone_number' => $validatedCompany['phone_number'],
                'email' => $validatedCompany['email'],
                'website' => $validatedCompany['website'] ?? null,
                'currency_id' => $validatedCompany['currency_id'] ?? null,
                //                'language_id' => $validatedCompany['language'] ?? null,
            ]
        );


        $user->companies()->attach(
            $newCompany->id,
            [
                'created_at' => Carbon::now(),
                'is_default' => true,
            ]
        );

        // User sanctum token
        $token = $user->createToken('myapptoken')->plainTextToken;
        $user->remember_token = $token;

        $roleName = $request->get('job_role') ?? 'Admin of company '.$newCompany->id;

        $user->createRole($roleName);

        $user->notify(new WelcomeNotification($user));

        $response = [
            'token' => $token,
            'user' => $user,
            'roles' => $user->roles()->select(['id', 'name', 'guard_name'])->get(),
            'company' => $newCompany,
        ];

        return new JsonResponse(['payload' => $response], Response::HTTP_CREATED);
    }

    /**
     * Login
     *
     * Authenticate existing user
     * @bodyParam email string. Example: admin@test.com
     * @bodyParam password string. Example: password
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $fields = $request->validated();

        $user = User::query()->where('email', $fields['email'])->first();

        if (!$user->email_verified_at) {
            throw new HttpException(403, 'User email is not verified');
        }

        // Check if user is found or not
        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], 404, ['Accept' => 'application/json']);
        }

        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        if (!Hash::check($fields['password'], $user->password)) {
            return new JsonResponse(['message' => 'Bad credentials'], 401, ['Accept' => 'application/json']);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $user->save();

        $response = [
            'message' => "Successful login",
            'token' => $token,
        ];

        return new JsonResponse(
            MeResource::make($user)
                ->additional(
                    array_merge(
                        $response,
                        [
                            'token' => $token,
                            'role' => $user->role,
                            'permissions' => AbilityResolver::groupAbilities($user->role),
                        ]
                    )
                )
        );
    }

    /**
     * Logout
     *
     * Logout authenticated user
     * @authenticated
     * @noinspection PhpUnusedParameterInspection
     * @param  Request  $request
     * @return array
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     * @noinspection PhpUndefinedMethodInspection
     */
    public function logout(Request $request): array
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => "Logged out"
        ];
    }

    /**
     * Verification email
     *
     * Send verification email
     * @authenticated
     * @param  Request  $request
     * @return array
     */
    public function sendVerificationEmail(Request $request): array
    {
        if ($request->user()->hasVerifiedEmail()) {
            return [
                'message' => 'Already Verified'
            ];
        }

        $request->user()->sendEmailVerificationNotification();

        return ['status' => 'verification-link-sent'];
    }


    /**
     * User verification
     *
     * Verify user email address
     * @authenticated
     * @param  string  $id
     * @param  string  $hash
     * @return string[]
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     */
    public function verify(string $id, string $hash)
    {
        $user = User::find($id);

        if (!$user || !hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return new JsonResponse(['message' => 'Invalid verification link.'], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return redirect('https://suppli.cloud');
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return redirect('https://suppli.cloud/login');
    }

    /**
     * Password recovery
     *
     * Send link for password reset
     * @param  Request  $request
     * @return array
     * @throws ValidationException
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     */
    public function forgotPassword(Request $request): array
    {
        $request->validate(
            [
                'email' => 'required|email'
            ]
        );

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            return [
                'status' => __($status)
            ];
        }

        throw ValidationException::withMessages(
            [
                'email' => [trans($status)]
            ]
        );
    }

    /**
     * Password reset
     *
     * Resets user password to a new one
     * @param  Request  $request
     * @return JsonResponse
     * @noinspection PhpUndefinedFieldInspection
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate(
            [
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|confirmed'
            ]
        );

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill(
                    [
                        'password' => Hash::make($request->password),
                        'remember_token' => Str::random(60),
                    ]
                )->save();

                $user->tokens()->delete();

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            //* DONE: get user from email and notify him/her using PasswordChangedNotification.php
            $user = User::query()->where('email', $request->email)->first();
            $user->notify(new PasswordChangedNotification());

            return new JsonResponse(
                [
                    'message' => 'Password reset successfully'
                ]
            );
        }

        return new JsonResponse(
            [
                'message' => __($status)
            ],
            500
        );
    }
}
