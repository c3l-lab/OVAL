@extends('layouts.app')

@section('title', 'OVAL Admin Page')


@section('content')
    <div class="container-fluid">
		<div class="page-title">
            <i class="fa fa-laptop"></i>
            MANAGE CONTENT ANALYSIS REQUESTS
        </div>
		@if (!empty(session('msg')))
        <div class="msg">
            {{ session('msg') }}
        </div>
        @endif

		<div class="admin-page-section-header">
            <h2>CURRENT REQUESTS</h2>
        </div><!-- admin-page-section-header -->

        <div class="admin-page-section">
        @if (count($current_requests)==0)
            <div class="text-content">
                There are no requests.
            </div>
        @else
            <div class="space-bottom space-left-right">
                <form action="{{ route('analysis_requests.batch_resend', ['id'=>1]) }}" method="POST" class="one-button-form">
                    {{ csrf_field() }}
                    <button type="submit" class="btn rectangle-button" id="approve-all" title="Approve all requests">
                        APPROVE ALL
                        <i class="fa fa-send" aria-hidden="true"></i>
                    </button>
                </form>
                <form action="{{ route('analysis_requests.batch_reject') }}" method="POST" class="one-button-form">
                    {{ csrf_field() }}
                    <button type="submit" class="btn rectangle-button" id="reject-all" title="Reject all requests">
                        REJECT ALL
                        <i class="fa fa-times-circle" aria-hidden="true"></i>
                    </button>
                </form>
            </div><!-- space-bottom -->

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>VIDEO</th>
                            <th>NO. OF REQUESTS</th>
                            <th>REQUESTED BY</th>
                            <th>REQUEST DATE</th>
                            <th>APPROVE</th>
                            <th>REJECT</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($current_requests as $r)
                        <tr>
                            <td>
                                <img src="{{ $r->video->thumbnail_url }}" class="video-thumbnail">
                                {{ $r->video->title }}
                            </td>
                            <td>{{ $r->numberOfReqForSameVideo() }}</td>
                            <td>
                                @foreach ($r->allRequestorsNames() as $name)
                                {{$name}}<br />
                                @endforeach
                            </td>
                            <td>{{ $r->created_at }}</td>
                            <td>
                                <form action="{{ route('analysis_requests.resend', ['analysis_request' => $r]) }}" method="POST">
                                    {{ csrf_field() }}
                                    <button type="submit" class="btn btn-link" title="Approve content analysis request">
                                        <i class="fa fa-send" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <form action="{{ route('analysis_requests.reject', ['analysis_request' => $r]) }}" method="POST" class="reject-form">
                                    {{ csrf_field() }}
                                    <button type="submit" class="btn btn-link" title="Reject analysis request">
                                        <i class="fa fa-times-circle" aria-hidden="true"></i>
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

        <div class="admin-page-section-header">
            <h2>REJECTED REQUESTS</h2>
        </div><!-- admin-page-section-header -->

        <div class="admin-page-section">
        @if (count($rejected_requests) == 0)
            <div class="text-content">
                There are no rejected requests.
            </div>
        @else
            <div class="space-bottom space-left-right">

                <form action="{{ route('analysis_requests.batch_recover') }}" method="POST"  class="one-button-form">
                    {{ csrf_field() }}
                    <button type="submit" class="btn rectangle-button" id="recover-all" title="Revert all rejected requests">
                        REVERT ALL
                        <i class="fa fa-undo" aria-hidden="true"></i>
                    </button>
                </form>

                <form action="{{ route('analysis_requests.batch_delete') }}" method="POST" class="one-button-form">
                    {{ csrf_field() }}
                    <button type="submit" class="btn rectangle-button" id="delete-all" title="Delete all rejected requests">
                        DELETE ALL
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </button>
                </form>
            </div><!-- space-bottom -->

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>VIDEO</th>
                            <th>NO. OF REQUESTS</th>
                            <th>REQUESTED BY</th>
                            <th>REQUEST DATE</th>
                            <th>REVERT</th>
                            <th>DELETE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rejected_requests as $r)
                        <tr>
                            <td>
                                <img src="{{ $r->video->thumbnail_url }}" class="video-thumbnail">
                                {{ $r->video->title }}
                            </td>
                            <td>{{ $r->numberOfReqForSameVideo() }}</td>
                            <td>
                                @foreach ($r->allRequestorsNames() as $name)
                                {{$name}}<br />
                                @endforeach
                            </td>
                            <td>{{ $r->created_at }}</td>
                            <td>
                                <form action="{{ route('analysis_requests.recover', ["analysis_request" => $r]) }}" method="POST">
                                	{{ csrf_field() }}
                                	<button type="submit" class="btn btn-link" title="Recover rejected request">
                                    	<i class="fa fa-undo" aria-hidden="true"></i>
                                	</button>
                                </form>
                            </td>
                            <td>
                                <form action="{{ route('analysis_requests.destroy', ["analysis_request" => $r]) }}" method="POST" class="delete-form">
                                    {{ method_field('DELETE') }}
                                    {{ csrf_field() }}
                                    <button type="submit" class="btn btn-link" title="Delete analysis request">
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






        <div class="admin-page-section-header">
            <h2>PROCESSED REQUESTS</h2>
        </div><!-- admin-page-section-header -->

        <div class="admin-page-section">
        @if (count($processed_requests)==0)
            <div class="text-content">
                There are no requests.
            </div>
        @else
            <div class="space-bottom space-left-right">
                <form action="{{ route('analysis_requests.batch_resend', ['id'=>1]) }}" method="POST" class="one-button-form">
                    {{ csrf_field() }}
                    <button type="submit" class="btn rectangle-button" id="resend-all" title="Re-send all requests">
                        RE-SEND ALL
                        <i class="fa fa-send" aria-hidden="true"></i>
                    </button>
                </form>
            </div><!-- space-bottom -->

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>VIDEO</th>
                            <th>NO. OF REQUESTS</th>
                            <th>REQUESTED BY</th>
                            <th>REQUEST DATE</th>
                            <th>RESEND</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($processed_requests as $r)
                        <tr>
                            <td>
                                <img src="{{ $r->video->thumbnail_url }}" class="video-thumbnail">
                                {{ $r->video->title }}

                            </td>
                            <td>{{ $r->numberOfReqForSameVideo() }}</td>
                            <td>


                                @foreach ($r->allRequestorsNames() as $name)
                                {{$name}}<br />
                                @endforeach
                            </td>
                            <td>{{ $r->created_at }}</td>
                            <td>
                                <form action="{{ route('analysis_requests.resend', ["analysis_request" => $r]) }}" method="POST">
                                    {{ csrf_field() }}
                                    <button type="submit" class="btn btn-link" title="Re-send content analysis request">
                                        <i class="fa fa-send" aria-hidden="true"></i>
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







        <div class="admin-page-section-header">
            <h2>GOOGLE CREDENTIALS</h2>
        </div><!-- admin-page-section-header -->
        <div class="admin-page-section">
            @if(count($google_creds)>0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Channel Name</th>
                            <th>Client ID</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($google_creds as $c)
                        <tr>
                            <td>{{$c->channel_title}}</td>
                            <td>{{$c->client_id}}</td>
                            <td>
                                <form action="/delete_google_cred" method="POST" role="form">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="g_cred_id" value="{{$c->id}}" />
                                    <button type="button" class="btn btn-link delete-cred-button">
                                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div><!-- table-responsive -->
            @else
                <div>There are currently no credentials stored. </div>
            @endif

            <div class="space-top-30 space-left-right">
                    <h3>Add new credential</h3>
                    <form id="add-cred-form" action="/add_google_cred" method="POST" role="form" data-toggle="validator">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="client-id-textbox">Client ID</label>
                            <input class="form-control gray-textbox" type="text" id="client-id-textbox" name="client_id" required>
                            <div class="help-block with-errors"></div>
                        </div><!-- form-group -->
                        <div class="form-group">
                            <label for="secret-textbox">Client Secret</label>
                            <input class="form-control gray-textbox" type="text" id="secret-textbox" name="secret" required>
                            <div class="help-block with-errors"></div>
                        </div><!-- form-group -->
                        <div class="form-group">
                            <button type="submit" id="add-cred-button" class="rectangle-button" title="Save">
                                SAVE
                                <i class="fa fa-floppy-o" aria-hidden="true"></i>
                            </button>
                        </div><!-- form-group -->
                    </form>

            </div><!-- row -->




        </div><!-- admin-page-section -->
	</div><!-- container-fluid -->
@endsection

@section('javascript')
 <script type="text/javascript" src="{{ URL::secureAsset('js/admin-page.js') }}"></script>
 <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.5/validator.min.js"></script>
@endsection
