<?php

namespace Tualo\Office\Revenuecat\Middlewares;

use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\IMiddleware;

class Middleware implements IMiddleware
{
    public static function register()
    {

        App::use('revenuecat', function () {
            try {
            } catch (\Exception $e) {
                App::set('maintanceMode', 'on');
                App::addError($e->getMessage());
            }
        }, 200);
    }
}
