<?php

namespace Tualo\Office\Revenuecat;

use Tualo\Office\Basic\TualoApplication as App;
use Ramsey\Uuid\Uuid;
use GuzzleHttp\Client;

class API
{

    private static $ENV = null;
    private static $gateway = null;

    public static function init(): void
    {
        self::getEnvironment();
    }

    public static function addEnvrionment(string $id, string $val)
    {
        self::$ENV[$id] = $val;
        $db = App::get('session')->getDB();
        try {
            if (!is_null($db)) {
                $db->direct('insert into revenuecat_environment (id,val) values ({id},{val}) on duplicate key update val=values(val)', [
                    'id' => $id,
                    'val' => $val
                ]);
            }
        } catch (\Exception $e) {
        }
    }

    public static function getEnvironment(): array
    {
        if (is_null(self::$ENV)) {
            $db = App::get('session')->getDB();
            try {
                self::$ENV = [];
                if (!is_null($db)) {
                    $data = $db->direct('select id,val from revenuecat_environment');
                    foreach ($data as $d) {
                        self::$ENV[$d['id']] = $d['val'];
                    }
                }
            } catch (\Exception $e) {
            }
            if (!isset(self::$ENV['base_url'])) {
                self::$ENV['base_url'] = 'https://api.revenuecat.com/v1/';
            }
        }
        return self::$ENV;
    }

    public static function replacer($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::replacer($value);
            }
            return $data;
        } else if (is_string($data)) {
            $env = self::getEnvironment();
            foreach ($env as $key => $value) {
                $data = str_replace('{{' . $key . '}}', $value, $data);
            }
            return $data;
        }
        return $data;
    }
    public static function env($key, $default = null)
    {
        $env = self::getEnvironment();
        if (isset($env[$key])) {
            return $env[$key];
        } else if (!is_null($default)) {
            return $default;
        }
        throw new \Exception('Environment ' . $key . ' not found!');
    }
    public static function client(bool $token = false): Client
    {
        $options = [
            'base_uri' => self::env('base_url'),
            'timeout'  => 2.0,
        ];
        if ($token) {
            $options['headers'] = [
                'Authorization' => 'Bearer ' . self::env('access_token')
            ];
        }
        return new Client($options);
    }

    public static function userProfile()
    {
        $response = self::client(true)->get('/v1/identity/openidconnect/userinfo?schema=openid');
        $code = $response->getStatusCode(); // 200
        $reason = $response->getReasonPhrase(); // OK

        if ($code != 200) {
            throw new \Exception($reason);
        }
        $result = json_decode($response->getBody()->getContents(), true);
        return $result;
    }
}
