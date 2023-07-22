<?php

namespace oval\Http\Controllers\Lti;

use Illuminate\Http\Request;
use oval\Http\Controllers\Controller;
use oval\Models\LtiRegistration;

class RegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('lti.registrations.index', [
            'registrations' => LtiRegistration::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return view('lti.registrations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $registration = new LtiRegistration();
        $registration->name = $request->name;
        $registration->issuer = $request->issuer;
        $registration->client_id = $request->client_id;
        $registration->deployment_id = $request->deployment_id;
        $registration->auth_token_url = $request->auth_token_url;
        $registration->auth_login_url = $request->auth_login_url;
        $registration->keyset_url = $request->keyset_url;
        $registration->generateKey();
        $registration->save();

        return redirect()->route('registrations.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('lti.registrations.edit', [
            'registration' => LtiRegistration::find($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $registration = LtiRegistration::find($id);
        $registration->name = $request->name;
        $registration->issuer = $request->issuer;
        $registration->client_id = $request->client_id;
        $registration->deployment_id = $request->deployment_id;
        $registration->auth_token_url = $request->auth_token_url;
        $registration->auth_login_url = $request->auth_login_url;
        $registration->keyset_url = $request->keyset_url;
        $registration->save();

        return redirect()->route('registrations.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $registration = LtiRegistration::find($id);
        $registration->delete();

        return redirect()->route('registrations.index');
    }
}
