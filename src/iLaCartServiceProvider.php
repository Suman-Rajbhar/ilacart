<?php
/**
 * Created by PhpStorm.
 * User: Lutfor Rahman
 * Email: contact.lutforrahman@gmail.com
 * Web: www.lutforrahman.com
 * GitHub: https://github.com/contactlutforrahman
 * Packagist: https://packagist.org/users/lutforrahman/
 * Date: 7/16/2016
 * Time: 12:07 PM
 */

namespace Lutforrahman\iLaCart;


use Illuminate\Support\ServiceProvider;

class iLaCartServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['cart'] = $this->app->share(function($app)
        {
            $session = $app['session'];
            $events = $app['events'];
            return new Cart($session, $events);
        });
    }
}