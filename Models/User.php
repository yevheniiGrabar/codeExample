<?php

/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use Stripe\Order;

/**
 * Class User
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $last_name
 * @property string $email
 * @property string $phone
 * @property int $country_id
 * @property int $language_id
 * @property int $currency_id
 * @property string $password
 * @property string $confirm_password
 * @property Carbon $created_at
 * @property mixed products
 * @property mixed currencies
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, Billable;

    protected $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'phone',
        'country_id',
        'language_id',
        'app_key',
        'client_key',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function sendPasswordResetNotification($token)
    {
        $url = env('REACT_WEBAPP_URL') . '/reset-password?email=' . $this->email . '&token=' . $token;

        // ? Here instead of notification, you can send direct email with a custom template.
        // ? Email address of the user can be specified by '$this->email'.
        // Send an email
        // Mail::to($this->email)->send(new ResetPasswordMail($url));
        $this->notify(new ResetPasswordNotification($url));
    }

    // user belongs to a company

    /**
     * @return BelongsToMany
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_user', 'user_id', 'company_id')->withPivot('is_default');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * @return HasMany
     */
    public function order(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * @return BelongsTo
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function currencies(): BelongsToMany
    {
        return $this->belongsToMany(Currency::class, 'user_currency')
            ->withPivot('is_base_currency');
    }


}
