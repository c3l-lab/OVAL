@extends('layouts.app')

@section('title', 'OVAL Admin Page - Manage LTI Registrations')


@section('content')
<div class="container-fluid">
    <div class="page-title">
        <i class="fa fa-laptop"></i>
        MANAGE LTI REGISTRATIONSS
    </div>
    @if (!empty(session('msg')))
    <div class="msg">
        {{ session('msg') }}
    </div>
    @endif

    @include('lti.registrations._form', ['registration' => $registration])
</div><!-- container-fluid -->


@endsection
