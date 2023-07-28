<?php

namespace oval\Http\Controllers\Lti;

use Illuminate\Http\Request;
use oval\Http\Controllers\Controller;
use oval\Models\LtiConsumer;
use DB;
use IMSGlobal\LTI\ToolProvider;
use IMSGlobal\LTI\ToolProvider\DataConnector;

class ConsumerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lti_connections = LtiConsumer::all();

        return view('pages.manage-lti', [
            'lti_connections' => $lti_connections
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $db_config = DB::getConfig();
            $conn_str = $db_config['driver'] . ':host=' . $db_config['host'] . ';port=' . $db_config['port'] . ';dbname=' . $db_config['database'];
            $pdo = new \PDO($conn_str, $db_config['username'], $db_config['password']);
        } catch (\PDOException $e) {
            return 'Connection failed: ' . $e->getMessage();
        }
        $db_connector = DataConnector\DataConnector::getDataConnector('', $pdo);
        $consumer = new ToolProvider\ToolConsumer($request->key, $db_connector);
        $consumer->name = $request->name;
        $consumer->secret = $request->secret;
        $consumer->enabled = true;
        $consumer->save();

        $consumer = LtiConsumer::where('consumer_key256', '=', $request->key)->first();

        $msg = "Connection was saved.";

        return back()->with(compact('msg'));
    }

    /**
     * Display the specified resource.
     */
    public function show(LtiConsumer $consumer)
    {
        $retval = [
            "name" => $consumer->name,
            "key" => $consumer->consumer_key256,
            "secret" => $consumer->secret,
            "from" => empty($consumer->enable_from) ? null : $consumer->enable_from->format('Y-m-d'),
            "to" => empty($consumer->enable_until) ? null : $consumer->enable_until->format('Y-m-d'),
        ];
        return $retval;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LtiConsumer $consumer)
    {
        $consumer->name = $request->name;
        $consumer->consumer_key256 = $request->key;
        $consumer->secret = $request->secret;
        $consumer->enable_from = empty($req->from) ? null : $request->from;
        $consumer->enable_until = empty($req->to) ? null : $request->to;
        $result = $consumer->save();

        return compact('result');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LtiConsumer $consumer)
    {
        $result = $consumer->delete();
        return compact('result');
    }
}
