<?php

namespace oval\Http\Controllers\Lti;

use Illuminate\Http\Request;
use oval\Http\Controllers\Controller;
use oval\LtiDeployment;

class DeploymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("lti.deployments.index", [
            'deployments' => \oval\LtiDeployment::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("lti.deployments.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $deployment = new LtiDeployment();
        $deployment->name = $request->name;
        $deployment->client_id = $request->client_id;
        $deployment->deployment_id = $request->deployment_id;
        $deployment->auth_token_url = $request->auth_token_url;
        $deployment->auth_login_url = $request->auth_login_url;
        $deployment->keyset_url = $request->keyset_url;
        $deployment->save();

        return redirect()->route('deployments.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('lti.deployments.edit', [
            'deployment' => LtiDeployment::find($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $deployment = LtiDeployment::find($id);
        $deployment->name = $request->name;
        $deployment->client_id = $request->client_id;
        $deployment->deployment_id = $request->deployment_id;
        $deployment->auth_token_url = $request->auth_token_url;
        $deployment->auth_login_url = $request->auth_login_url;
        $deployment->keyset_url = $request->keyset_url;
        $deployment->save();

        return redirect()->route('deployments.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $deployment = LtiDeployment::find($id);
        $deployment->delete();

        return redirect()->route('deployments.index');
    }
}
