<?php

namespace App\Providers;

use App\Models\Admin;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    public function boot()
    {
        //

        parent::boot();
    }

    public function map()
    {
        Route::group(['namespace' => $this->namespace, 'middleware' => 'web'], function ($router) {

            require app_path('Http/Routes/landings.php');

            require app_path('Http/Routes/country.php');
            require app_path('Http/Routes/city.php');

            require app_path('Http/Routes/work.php');
            require app_path('Http/Routes/game.php');
            require app_path('Http/Routes/tag.php');
            require app_path('Http/Routes/color.php');
            require app_path('Http/Routes/shortlist.php');
            require app_path('Http/Routes/rating.php');
            require app_path('Http/Routes/comment.php');

            require app_path('Http/Routes/user.php');
            require app_path('Http/Routes/auth.php');
            require app_path('Http/Routes/pay.php');
            require app_path('Http/Routes/service_tag.php');

            require app_path('Http/Routes/event.php');
            require app_path('Http/Routes/poster.php');
            require app_path('Http/Routes/blog.php');

            require app_path('Http/Routes/picture.php');

            require app_path('Http/Routes/subscribe.php');
            require app_path('Http/Routes/sitemap.php');
            require app_path('Http/Routes/service.php');
            require app_path('Http/Routes/static.php');
            require app_path('Http/Routes/producer.php');

            require app_path('Http/Routes/championship.php');
            require app_path('Http/Routes/university.php');
            require app_path('Http/Routes/lessons.php');

            //...

        });
    }
}
