<?php

namespace app\database;

use Predis\Client;

class ConnectionRedis {
    private static $redis;

    private static function init() {
        if (!self::$redis) {
            self::$redis = new Client([
                'scheme' => 'tcp',
                'host' => '127.0.0.1',
                'port' => 6379
            ]);
        }
    }

    public static function setData(string $SQLtable, int|string $SQLid, string $name, array $value) {
        if (isset($SQLid)) {
            self::init();

            $redisKey = "{$SQLtable}:{$SQLid}:{$name}";

            self::$redis->set($redisKey, json_encode($value));
        }
    }

    public static function getData(string $SQLtable, int|string $SQLid, string $name) {
        if (isset($SQLid)) {
            self::init();

            $redisKey = "{$SQLtable}:{$SQLid}:{$name}";

            if (self::$redis->exists($redisKey)) {
                return json_decode(self::$redis->get($redisKey), true);
            }

            return null;
        }
    }

    public static function deleteData(string $SQLtable, int|string $SQLid, string $name) {
        if (isset($SQLid)) {
            self::init();

            $redisKey = "{$SQLtable}:{$SQLid}:{$name}";

            self::$redis->del($redisKey);
        }
    }

    public static function updateData(string $SQLtable, int|string $SQLid, string $name, array $newValue) {
        if (isset($SQLid)) {
            self::init();

            $redisKey = "{$SQLtable}:{$SQLid}:{$name}";

            if (self::$redis->exists($redisKey)) {
                $currentData = json_decode(self::$redis->get($redisKey), true);
                $updatedData = array_merge($currentData, $newValue);

                self::$redis->set($redisKey, json_encode($updatedData));

            } else {
                self::setData($SQLtable, $SQLid, $name, $newValue);
            }
        }
    }

    public static function getAllData(string $pattern): array {
        self::init();

        $keys = self::$redis->keys($pattern);
        $allData = [];

        foreach ($keys as $key) {
            $data = self::$redis->get($key);
            $allData[$key] = json_decode($data, true);
        }

        return $allData;
    }
}
