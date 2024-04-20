<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*', 'App\Http\ViewComposers\SettingsComposer');
        view()->composer(
            ['home','productlisting_page','productdetails','frontview_customer.wishlist'], 'App\Http\ViewComposers\ProductCommonComposer'
        );
		 
            \URL::forceScheme('https');
       
    }
}
