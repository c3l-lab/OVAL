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

        <div class="admin-page-section-header">
            <h2>EXISTING REGISTRATIONSS</h2>
        </div><!-- admin-page-section-header -->

        <div class="admin-page-section">
            <div class="toolbar">
                <a href="/lti/registrations/create">
                    <button type="button" class="rectangle-button btn">
                        Add
                    </button>
                </a>
            </div>
            @if (count($registrations) == 0)
                There are no LTI registrations configured.
            @else
                <div class="table-responsive">
                    <table class="table table-striped" id="lti-registrations-table">
                        <thead>
                            <tr>
                                <th>NAME</th>
                                <th>ISSUER</th>
                                <th>EDIT</th>
                                <th>DELETE</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($registrations as $registration)
                                <tr>
                                    <td>{{ $registration->name }}</td>
                                    <td>{{ $registration->issuer }}</td>
                                    <td>
                                        <a href="/lti/registrations/{{ $registration->id }}/edit">
                                            <button type="button" class="btn btn-link edit-lti-button"
                                                title="Edit LTI registration">
                                                <i class="fa fa-pencil" aria-hidden="true"></i>
                                            </button>
                                        </a>
                                    </td>
                                    <td>
                                        <form method="POST" action="/lti/registrations/{{ $registration->id }}">
                                            @method('DELETE')
                                            @csrf
                                            <button type="submit" class="btn btn-link delete-lti-button" title="Delete LTI registration">
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
