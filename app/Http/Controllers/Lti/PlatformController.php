<?php

namespace oval\Http\Controllers\Lti;

use Illuminate\Http\Request;
use oval\Http\Controllers\Controller;
use oval\LtiPlatform;

class PlatformController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
		return view('lti.platforms.index', [
            'platforms' => LtiPlatform::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return view('lti.platforms.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $platform = new LtiPlatform();
        $platform->name = $request->name;
        $platform->iss = $request->iss;
        $platform->save();

        return redirect('/lti/platforms');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('lti.platforms.create', [
            'platform' => LtiPlatform::find($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $platform = LtiPlatform::find($id);
        $platform->name = $request->name;
        $platform->iss = $request->iss;
        $platform->save();

        return redirect('/lti/platforms');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $platform = LtiPlatform::find($id);
        $platform->delete();

        return redirect('/lti/platforms');
    }
}
