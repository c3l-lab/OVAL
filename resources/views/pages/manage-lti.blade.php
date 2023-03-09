@extends('layouts.app')

@section('title', 'OVAL Admin Page - Batch Upload')


@section('content')
<div class="container-fluid">
    <div class="page-title">
        <i class="fa fa-laptop"></i>
        MANAGE LTI CONNECTION
    </div>
    @if (!empty(session('msg')))
    <div class="msg">
        {{ session('msg') }}
    </div>
    @endif

    <div class="admin-page-section-header">
        <h2>EXISTING CONNECTIONS</h2>
    </div><!-- admin-page-section-header -->

    <div class="admin-page-section">
        @if (count($lti_connections) == 0)
        There are no LTI connections configured.
        @else
        <div class="table-responsive">
            <table class="table table-striped" id="lti-connections-table">
                <thead>
                    <tr>
                        <th>NAME</th>
                        <th>KEY</th>
                        <th>SECRET</th>
                        <th>DATE</th>
                        <th>EDIT</th>
                        <th>DELETE</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lti_connections as $c)

                    <tr>
                        <td>{{$c->name}}</td>
                        <td>{{{$c->consumer_key256}}}</td>
                        <td>{{{$c->secret}}}</td>
                        <td>
                            @isset($c->enable_from)
                            {{$c->enable_from}}<br />
                            @endisset
                            @isset($c->enable_until)
                            {{$c->enable_until}}
                            @endisset
                            &nbsp;
                        </td>
                        <td>
                            <button type="button" class="btn btn-link edit-lti-button" data-id="{{$c->consumer_pk}}" data-toggle="modal" data-target="#edit-lti-modal" data-id="{{$c->consumer_pk}}" title="Edit LTI connection">
                                <i class="fa fa-pencil" aria-hidden="true"></i>
                            </button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-link delete-lti-button" data-id="{{$c->consumer_pk}}" title="Delete LTI connection">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div><!-- table-responsive -->
        @endif
    </div><!-- admin-page-section -->



    <div class="admin-page-section-header">
        <h2>ADD NEW CONNECTION</h2>
    </div><!-- admin-page-section-header -->

    <div class="admin-page-section">
        <div class="space-left-right">
            <form id="add-lti-form" method="POST" action="/add_lti_connection" role="form" data-toggle="validator">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="lti-connection-name">Name</label>
                            <input type="text" id="lti-connection-name" class="form-control gray-textbox" name="name">
                        </div><!-- form-group -->

                        <fieldset class="form-group row">
                            <legend class="col-xs-12">Key</legend>
                            <div class="col-xs-10">  
                                <input type="text" id="lti-connection-key" class="form-control gray-textbox" name="key" required>
                            </div>
                            <div class="col-xs-2">
                                <button type="button" id="generate-key" class="rectangle-button textonly">Generate</button>
                            </div>
                            <div class="col-xs-12 help-block with-errors"></div>
                        </fieldset>

                        <fieldset class="form-group row">
                            <legend class="col-xs-12">Secret</legend>
                            <div class="col-xs-10">  
                                <input type="text" id="lti-connection-secret" class="form-control gray-textbox" name="secret" required>
                            </div>
                            <div class="col-xs-2">
                                <button type="button" id="generate-secret" class="rectangle-button textonly">Generate</button>
                            </div>
                            <div class="col-xs-12 help-block with-errors"></div>
                        </fieldset>

                        <div class="form-group">
                            <label for="lti-connection-from">From</label>
                            <input type="date" id="lti-connection-from" class="form-control gray-textbox" name="from_date">
                        </div>

                        <div class="form-group">
                            <label for="lti-connection-to">To</label>
                            <input type="date" id="lti-connection-to" class="form-control gray-textbox" name="to_date">
                        </div>
  
                    </div><!-- col -->

                    <div class="col-xs-12 col-md-6">
                        <fieldset>
                            <legend>Database Credential</legend>
                            
                            <div class="form-group col-xs-12">
                                <label for="lti-db-type">DB type</label>
                                <input id="lti-db-type" class="form-control gray-textbox" name="db_type">
                            </div>
                            <div class="form-group col-xs-12">
                                <label for="lti-db-host">Host</label>
                                <input id="lti-db-host" class="form-control gray-textbox" name="host">
                            </div>
                            <div class="form-group col-xs-12">
                                <label for="lti-db-port">Port</label>
                                <input id="lti-db-port" class="form-control gray-textbox" name="port">
                            </div>
                            <div class="form-group col-xs-12">
                                <label for="lti-db-name">DB Name</label>
                                <input id="lti-db-name" class="form-control gray-textbox" name="db_name">
                            </div>
                            <div class="form-group col-xs-12">
                                <label for="lti-db-un">User Name</label>
                                <input id="lti-db-un" class="form-control gray-textbox" name="user">
                            </div>
                            <div class="form-group col-xs-12">
                                <label for="lti-db-pw">Password</label>
                                <input id="lti-db-pw" class="form-control gray-textbox" name="pw">
                            </div>
                            <div class="form-group col-xs-12">
                                <label for="lti-db-prefix">Table Prefix</label>
                                <input id="lti-db-prefix" class="form-control gray-textbox" name="prefix">
                            </div>
                        </fieldset>
                    </div><!-- col -->
                </div><!-- row -->

                <button type="submit" class="rectangle-button btn">
                    SAVE
                    <i class="fa fa-floppy-o" aria-hidden="true"></i>
                </button>

            </form>
        </div><!-- space-left-right -->
    </div><!-- admin-page-section -->

</div><!-- container-fluid -->


@endsection

@section('modal')
	@include('parts.manage-lti-modal')
@endsection

@section('javascript')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.5/validator.min.js"></script>
    <script type="text/javascript" src="{{ URL::secureAsset('js/manage-lti.js') }}"></script>
@endsection