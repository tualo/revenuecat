<?php

namespace Tualo\Office\Revenuecat\Routes;

use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;
use Tualo\Office\Revenuecat\API;

class Test implements IRoute
{

    public static function register()
    {
        BasicRoute::add('/revenuecat/(?P<id>.+)', function ($matches) {
            try {
                $db = App::get('session')->getDB();

                $data = API::getSubscriptions($matches['id']);
                App::result('success', true);
                App::result('data', $data);
                App::contenttype('application/json');
            } catch (\Exception $e) {
                echo $e->getMessage();
                http_response_code(400);
            }
        }, ['get'], true);
    }
}
