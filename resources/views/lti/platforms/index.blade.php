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

        <div class="admin-page-section-header">
            <h2>EXISTING PLATFORMS</h2>
        </div><!-- admin-page-section-header -->

        <div class="admin-page-section">
            <div class="toolbar">
                <a href="/lti/platforms/create">
                    <button type="button" class="rectangle-button btn">
                        Add
                    </button>
                </a>
            </div>
            @if (count($platforms) == 0)
                There are no LTI platforms configured.
            @else
                <div class="table-responsive">
                    <table class="table table-striped" id="lti-platforms-table">
                        <thead>
                            <tr>
                                <th>NAME</th>
                                <th>ISSUER</th>
                                <th>EDIT</th>
                                <th>DELETE</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($platforms as $platform)
                                <tr>
                                    <td>{{ $platform->name }}</td>
                                    <td>{{ $platform->iss }}</td>
                                    <td>
                                        <a href="/lti/platforms/{{ $platform->id }}/edit">
                                            <button type="button" class="btn btn-link edit-lti-button"
                                                title="Edit LTI connection">
                                                <i class="fa fa-pencil" aria-hidden="true"></i>
                                            </button>
                                        </a>
                                    </td>
                                    <td>
                                        <form method="POST" action="/lti/platforms/{{ $platform->id }}">
                                            @method('DELETE')
                                            @csrf
                                            <button type="submit" class="btn btn-link delete-lti-button"
                                                data-id="{{ $platform->consumer_pk }}" title="Delete LTI connection">
                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div><!-- table-responsive -->
            @endif
        </div><!-- admin-page-section -->

    </div><!-- container-fluid -->


@endsection
