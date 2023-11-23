<?php

namespace App\Providers;

use App\Enums\Users\RolesEnum;
use App\Models\Category;
use App\Models\Company;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductionOrder;
use App\Models\User;
use App\Policies\CategoryPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\LocationPolicy;
use App\Policies\ProductionOrderPolicy;
use App\Policies\ProductPolicy;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Notifications\Messages\MailMessage;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Product::class => ProductPolicy::class,
        Company::class => CompanyPolicy::class,
        Category::class => CategoryPolicy::class,
        ProductionOrder::class => ProductionOrderPolicy::class,
        Location::class => LocationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @param Gate $gate
     * @return void
     */
    public function boot(Gate $gate)
    {
        $this->registerPolicies();

        // * Send Email verification mail
        VerifyEmail::toMailUsing(function($notifiable, $url) {
            $btnUrl = env('REACT_WEBAPP_URL') . "/email-verify?url=" . $url;

            return (new MailMessage)
                ->markdown('emails.verify-email')
                ->action('Verify Email', $btnUrl);
        });

        // Check admin role before checking permissions
        $gate->before(function (User $user) {
            if ($user->hasRole(RolesEnum::SUPER_ADMIN)) {
                return true;
            }
        });
    }
}
