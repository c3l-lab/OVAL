<?php

namespace oval\Services;

use InfluxDB2\Client;
use InfluxDB2\Point;
use InfluxDB2\Model\WritePrecision;
use InvalidArgumentException;

class InfluxDBService
{
    private $client;

    public function __construct()
    {
        $host = config('database.connections.influxdb.host');
        $port = config('database.connections.influxdb.port');
        $protocol = config('database.connections.influxdb.protocol');
        
        $this->client = new Client([
            "url" => "$protocol://$host:$port",
            "token" => config('database.connections.influxdb.token'),
            "org" => config('database.connections.influxdb.org'),
            "bucket" => config('database.connections.influxdb.bucket'),
            "precision" => WritePrecision::MS
        ]);
    }

    public function queryStream(string $duration = null, int $gv_id = null) {
        if ($duration && !$this->isValidDuration($duration)) {
            return [];
        }

        $queryApi = $this->client->createQueryApi();
        $queryString = 'from(bucket: "oval_ts")';
        
        if($duration) $queryString .= " |> range(start: -{$duration})";
        else $queryString .= " |> range(start: -100y)";
        
        $queryString .= ' |> filter(fn: (r) => r._measurement == "eye_tracking")';
        if($gv_id) $queryString .= " |> filter(fn: (r) => r.gv_id == \"{$gv_id}\")";
        $queryString .= ' |> pivot(rowKey:["_time"], columnKey: ["_field"], valueColumn: "_value")';

        return $queryApi->queryStream($queryString);
    }

    public function insertRecords($records)
    {
        $writeApi = $this->client->createWriteApi();
        $writeApi->write($records);
        $writeApi->close();
    }

    public function createRecordWithTimestamp($time, $fields, $tags = null) 
    {
        if (is_null($time)) {
            throw new InvalidArgumentException("Time parameter cannot be null.");
        }
        if (!is_array($fields) || empty($fields)) {
            throw new InvalidArgumentException("Fields must be a non-empty array.");
        }

        $point = Point::measurement('eye_tracking')
                    ->time($time, WritePrecision::MS);

        foreach ($fields as $key => $value) {
            if(is_numeric($value)){
                $point->addField($key, (float) $value);
            } else {
                $point->addField($key, $value);
            }
        }

        if (is_array($tags) && !empty($tags)) {
            foreach ($tags as $key => $value) {
                if(is_numeric($value)){
                    $point->addTag($key, (int) $value);
                } else {
                    $point->addTag($key, $value);
                }
            }
        }

        return $point;
    }

    private function isValidDuration(string $duration): bool {
        $pattern = '/^-?\d+(s|m|h|d|w|mo|y)$/';
        return preg_match($pattern, $duration) === 1;
    }

    public function __destruct() {
        $this->client->close();
    }
}