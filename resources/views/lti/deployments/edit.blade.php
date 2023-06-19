@extends('layouts.app')

@section('title', 'OVAL Admin Page - Manage LTI Deployments')


@section('content')
<div class="container-fluid">
    <div class="page-title">
        <i class="fa fa-laptop"></i>
        MANAGE LTI DEPLOYMENTS
    </div>
    @if (!empty(session('msg')))
    <div class="msg">
        {{ session('msg') }}
    </div>
    @endif

    @include('lti.deployments._form', ['deployment' => $deployment])
</div><!-- container-fluid -->


@endsection
