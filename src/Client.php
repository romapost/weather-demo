<?php

declare(strict_types=1);

namespace Weather;

use DateInterval;
use DateTime;
use Exception;
use Redis;
use Throwable;

/**
 * Class Weather Client
 * @package Weather
 */
final class Client
{
    /**
     * Country code
     */
    public const COUNTRY_CODE = 'RU';

    /**
     * City
     */
    public const CITY_NAME = 'Moscow';

    /**
     * API Key
     */
    public const API_KEY = 'b015b385ee562cc36d3541afbcc0838f';

    /**
     * Api point current
     */
    public const API_URL = 'https://api.openweathermap.org/data/2.5/weather?q=' . self::CITY_NAME . '&appid=' . self::API_KEY;

    /**
     * Api point history
     */
    public const API_URL_HISTORY = 'https://history.openweathermap.org/data/2.5/history/city?q=' . self::CITY_NAME . ', ' . self::COUNTRY_CODE . '&type=hour&appid=' . self::API_KEY;

    /**
     * Path save data
     */
    public const SAVE_PATH = __DIR__ . '/../data/';

    /**
     * Get current weather data
     */
    public static function getCurrentData(): array
    {
        $data = '';
        try {
            $data = file_get_contents(self::API_URL);
            if (!$data) {
                throw new Exception('Network');
            }
        } catch (Throwable $exc) {
            echo 'Error:' . $exc->getMessage();
        }
        return json_decode($data, true);
    }

    /**
     * Get history weather data
     */
    public static function getHistoryData(): array
    {
        $data = '';
        try {
            $data = file_get_contents(self::API_URL_HISTORY);
            if (!$data) {
                throw new Exception('Network');
            }
        } catch (Throwable $exc) {
            echo 'Error:' . $exc->getMessage();
        }

        return json_decode($data, true);
    }

    /**
     * Save to file
     * @throws Exception
     */
    public function saveToFile(): void
    {
        $data = self::getCurrentData();

        $result = [];
        $result['datetime'] = (new DateTime("@{$data['dt']}"))
            ->add(new DateInterval("PT{$data['timezone']}S"))
            ->format('Y-m-d H:i');

        $result = array_merge($result, $data['main']);

        $file = self::SAVE_PATH . 'data.json';
        if (!file_put_contents($file, json_encode($result))) {
            throw new Exception('Saving file');
        }
    }

    /**
     * Save to storage(ex. Redis)
     * @throws Exception
     */
    public function saveToStorage(): void
    {
        $data = self::getCurrentData();

        $redis = new Redis();
        if (!$redis->connect('localhost')) {
            throw new Exception('Redis no connected: ' . $redis->getLastError());
        }

        $key = 'weather_' . $data['dt'];
        $redis->setex($key, 10800, serialize($data));

        //var_dump(unserialize($redis->get($key)));
    }
}
