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

        <div class="admin-page-section-header">
            <h2>EXISTING DEPLOYMENTS</h2>
        </div><!-- admin-page-section-header -->

        <div class="admin-page-section">
            <div class="toolbar">
                <a href="/lti/deployments/create">
                    <button type="button" class="rectangle-button btn">
                        Add
                    </button>
                </a>
            </div>
            @if (count($deployments) == 0)
                There are no LTI deployments configured.
            @else
                <div class="table-responsive">
                    <table class="table table-striped" id="lti-deployments-table">
                        <thead>
                            <tr>
                                <th>NAME</th>
                                <th>CLIENT ID</th>
                                <th>DEPLOYMENT ID</th>
                                <th>EDIT</th>
                                <th>DELETE</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($deployments as $deployment)
                                <tr>
                                    <td>{{ $deployment->name }}</td>
                                    <td>{{ $deployment->client_id }}</td>
                                    <td>{{ $deployment->deployment_id }}</td>
                                    <td>
                                        <a href="/lti/deployments/{{ $deployment->id }}/edit">
                                            <button type="button" class="btn btn-link edit-lti-button"
                                                title="Edit LTI deployment">
                                                <i class="fa fa-pencil" aria-hidden="true"></i>
                                            </button>
                                        </a>
                                    </td>
                                    <td>
                                        <form method="POST" action="/lti/deployments/{{ $deployment->id }}">
                                            @method('DELETE')
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-link delete-lti-button"
                                                title="Delete LTI deployment">
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
