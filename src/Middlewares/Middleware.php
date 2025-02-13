<?php

namespace Tualo\Office\Revenuecat\Middlewares;

use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\IMiddleware;
use Tualo\Office\Revenuecat\API;

class Middleware implements IMiddleware
{
    public static function register()
    {

        App::use('revenuecat', function () {
            try {
                $session = App::get('session');
                if ($session->getHeader('revenuecat_user_id')) {
                    $db = App::get('session')->getDB();


                    $data = $db->direct('select * from revenuecat_subscriptions where customer_id = {revenuecat_user_id} and current_timestamp() between current_period_starts_at and current_period_ends_at', [
                        'revenuecat_user_id' => $session->getHeader('revenuecat_user_id')
                    ]);
                    if (count($data) == 0) {
                        $data = API::getSubscriptions($session->getHeader('revenuecat_user_id'));
                        foreach ($data as $d) {

                            $d['current_period_ends_at'] = $d['current_period_ends_at'] / 1000;
                            $d['current_period_starts_at'] = $d['current_period_starts_at'] / 1000;
                            $db->direct('insert into revenuecat_subscriptions (
                                id,
                                customer_id,
                                current_period_ends_at,
                                current_period_starts_at,
                                product_id
                            ) values (
                                {id},
                                {customer_id},
                                {current_period_ends_at},
                                {current_period_starts_at},
                                {product_id}
                            )', $d);
                        }
                    }

                    $db->direct('set @revenuecat_user_id = {revenuecat_user_id}', [
                        'revenuecat_user_id' => $session->getHeader('revenuecat_user_id')
                    ]);
                }
            } catch (\Exception $e) {
                App::set('maintanceMode', 'on');
                App::addError($e->getMessage());
            }
        }, 0, [], true);
    }
}
