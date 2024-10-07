<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;
use Illuminate\Support\Facades\View;
use App\Models\Menu;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
        Schema::defaultStringLength(191);

        try{
            $categories = Category::with('children:id,c_name,c_slug,c_parent_id')
                ->where('c_parent_id',0)
                ->select('id','c_name','c_slug','c_avatar','c_parent_id')
                ->get();

            View::share('categories', $categories);


            $categoriesHot = Category::where('c_hot', 1)->select('id','c_name','c_slug','c_avatar')->get();
            View::share('categoriesHot', $categoriesHot);

            $menus = Menu::select('id','mn_name','mn_slug')->get();
            View::share('menus',$menus);
        }catch (\Exception $exception) {

        }
    }

    protected function authenticateBasic()
    {
        if (!isset($_SERVER['PHP_AUTH_USER']) && !isset($_SERVER['PHP_AUTH_PW']))
        {
            header("WWW-Authenticate: Basic realm=\"Please enter your username and password to proceed further\"");
            header("HTTP/1.0 401 Unauthorized");
            print "Oops! It require login to proceed further. Please enter your login detail\n";
            exit;
        }
        else
        {
            if ($_SERVER['PHP_AUTH_USER'] == 'phudz@gmail.com' && $_SERVER['PHP_AUTH_PW'] == 'phudz@gmail.com')
            {

            }
            else
            {
                header("WWW-Authenticate: Basic realm=\"Please enter your username and password to proceed further\"");
                header("HTTP/1.0 401 Unauthorized");
                print "Oops! It require login to proceed further. Please enter your login detail\n";
                exit;
            }
        }
    }
}
