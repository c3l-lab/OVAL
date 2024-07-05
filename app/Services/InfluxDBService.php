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
        $host = env('INFLUXDB_HOST', 'localhost');
        $port = env('INFLUXDB_PORT', '8086');
        $protocol = env('INFLUXDB_PROTOCOL', 'http');
        
        $this->client = new Client([
            "url" => "$protocol://$host:$port",
            "token" => env('INFLUXDB_TOKEN', 'my_token'),
            "org" => env('INFLUXDB_ORG', 'oval'),
            "bucket" => env('INFLUXDB_BUCKET', 'oval_ts'),
            "precision" => WritePrecision::MS
        ]);
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
                $point->addField($key, (double) $value);
            } else {
                $point->addField($key, $value);
            }
        }

        if (is_array($tags) && !empty($tags)) {
            foreach ($tags as $key => $value) {
                $point->addTag($key, $value);
            }
        }

        return $point;
    }

    public function queryDatabase($fluxQuery)
    {
        $queryApi = $this->client->createQueryApi();
        return $queryApi->query($fluxQuery, config('database.influxdb.org'));
    }
}