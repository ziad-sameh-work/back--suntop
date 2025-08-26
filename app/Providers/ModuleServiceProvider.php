<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Register module services
        $this->registerAuthServices();
        $this->registerProductServices();
        $this->registerOrderServices();
        $this->registerMerchantServices();
        $this->registerLoyaltyServices();
        $this->registerOfferServices();
        $this->registerUserCategoryServices();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register Auth module services
     */
    private function registerAuthServices()
    {
        $this->app->bind(
            \App\Modules\Auth\Services\AuthService::class,
            function ($app) {
                return new \App\Modules\Auth\Services\AuthService(
                    $app->make(\App\Models\User::class)
                );
            }
        );
    }

    /**
     * Register Product module services
     */
    private function registerProductServices()
    {
        $this->app->bind(
            \App\Modules\Products\Services\ProductService::class,
            function ($app) {
                return new \App\Modules\Products\Services\ProductService(
                    $app->make(\App\Modules\Products\Models\Product::class)
                );
            }
        );
    }

    /**
     * Register Order module services
     */
    private function registerOrderServices()
    {
        $this->app->bind(
            \App\Modules\Orders\Services\OrderService::class,
            function ($app) {
                return new \App\Modules\Orders\Services\OrderService(
                    $app->make(\App\Modules\Orders\Models\Order::class),
                    $app->make(\App\Modules\Products\Services\ProductService::class),
                    $app->make(\App\Modules\Merchants\Services\MerchantService::class),
                    $app->make(\App\Modules\Loyalty\Services\LoyaltyService::class),
                    $app->make(\App\Modules\Offers\Services\OfferService::class),
                    $app->make(\App\Modules\Users\Services\UserCategoryService::class)
                );
            }
        );
    }

    /**
     * Register Merchant module services
     */
    private function registerMerchantServices()
    {
        $this->app->bind(
            \App\Modules\Merchants\Services\MerchantService::class,
            function ($app) {
                return new \App\Modules\Merchants\Services\MerchantService(
                    $app->make(\App\Modules\Merchants\Models\Merchant::class)
                );
            }
        );
    }

    /**
     * Register Loyalty module services
     */
    private function registerLoyaltyServices()
    {
        $this->app->bind(
            \App\Modules\Loyalty\Services\LoyaltyService::class,
            function ($app) {
                return new \App\Modules\Loyalty\Services\LoyaltyService(
                    $app->make(\App\Modules\Loyalty\Models\LoyaltyPoint::class)
                );
            }
        );
    }

    /**
     * Register Offer module services
     */
    private function registerOfferServices()
    {
        $this->app->bind(
            \App\Modules\Offers\Services\OfferService::class,
            function ($app) {
                return new \App\Modules\Offers\Services\OfferService(
                    $app->make(\App\Modules\Offers\Models\Offer::class)
                );
            }
        );
    }

    /**
     * Register User Category module services
     */
    private function registerUserCategoryServices()
    {
        $this->app->bind(
            \App\Modules\Users\Services\UserCategoryService::class,
            function ($app) {
                return new \App\Modules\Users\Services\UserCategoryService(
                    $app->make(\App\Modules\Users\Models\UserCategory::class)
                );
            }
        );
    }
}
