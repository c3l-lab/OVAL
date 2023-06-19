@extends('layouts.app')

@section('title', 'OVAL Admin Page - Manage LTI Platforms')


@section('content')
<div class="container-fluid">
    <div class="page-title">
        <i class="fa fa-laptop"></i>
        MANAGE LTI PLATFORMS
    </div>
    @if (!empty(session('msg')))
    <div class="msg">
        {{ session('msg') }}
    </div>
    @endif

    @include('lti.platforms._form', ['platform' => $platform])
</div><!-- container-fluid -->


@endsection
