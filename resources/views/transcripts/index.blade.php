@extends('layouts.app')

@section('title', 'OVAL Admin Page - Batch Upload')


@section('content')
    <div class="container-fluid">
		<div class="page-title">
            <i class="fa fa-laptop"></i>
            UPLOAD DATA
        </div>
		@if (!empty(session('msg')))
        <div class="msg">
            {{ session('msg') }}
        </div>
        @endif


        <div class="admin-page-section-header">
            <h2>UPLOAD JSON FILE</h2>
        </div><!-- admin-page-section-header -->

        <div class="admin-page-section">

        <div class="space-left-right">
                <form id="upload-json-form" method="POST" action="{{ route('transcripts.upload') }}" enctype="multipart/form-data" role="form" data-toggle="validator">
                    {{ csrf_field() }}
                    <input type="hidden" name="video_id" />
                    <div class="form-group">
                        <label for="transcript-file">Please select JSON file to upload...</label>
                        <input type="file" id="batch-data-file" name="file" data-filetype="json" data-required-error="Please select a file in .json format" required>
                        <div class="help-block with-errors"></div>



                    </div><!-- form-group -->
                    <div class="form-group">
                        <button type="submit" class="btn rectangle-button" id="upload">
                            Upload
                            <i class="fa fa-upload left-indent" aria-hidden="true"></i>
                        </button>
                    </div>

                </form>
            </div><!-- space -->

            <div class="space-top-30 space-left-right">
                <p>
                    NOTE:<br />
                    JSON document has to contain array of objects "transcripts" or "identifiers".<br />
                </p>
                <p>
                    For uploading transcripts, it contains objects with variables "identifier" and "transcript".<br />
                    If array of YouTube video IDs are contained in this JSON document,
                    YouTube Data API is used to fetch their transcripts.<br />
                    Text analysis is performed in either case after populating transcripts table.
                </p>
                <p>
                    Sample format below.<br />
                </p>
                <pre>
                {
                    "transcripts": [
                        {
                            "identifier": "MKoroBS8Ke4",
                            "transcript": ["{\"start\":0.03,\"end\":5.759,\"transcript\":\"Hello and welcome. It's my pleasure tointroduce you to the Bachelor of IT and\"}", "{\"start\":5.759,\"end\":12.12,\"transcript\":\"Data Analytics degree at UniSA Online.My name is Dale Wache, I've worked with\"}", "{\"start\":12.12,\"end\":16.56,\"transcript\":\"lecturers at this University for thepast 20 years to promote and enhance\"}", "{\"start\":16.56,\"end\":20.369,\"transcript\":\"teaching and learning especially in theonline environment.\"}", "{\"start\":20.369,\"end\":24.99,\"transcript\":\"More specifically I've worked withcolleagues in the school of IT and maths\"}", "{\"start\":24.99,\"end\":30.17,\"transcript\":\"to prepare courses and The Bachelor ofIT and Data Analytics for online delivery.\"}", "{\"start\":30.17,\"end\":36.54,\"transcript\":\"Kath Moore, your online course facilitator,and I passionate about your learning in\"}", "{\"start\":36.54,\"end\":41.7,\"transcript\":\"the field of IT. Together we are excitedto bring you an interesting, well\"}", "{\"start\":41.7,\"end\":46.02,\"transcript\":\"designed curriculum to enhance yourprofessional skills and knowledge in\"}", "{\"start\":46.02,\"end\":52.17,\"transcript\":\"these areas. The IT and Data Analyticsdegree provides you with an industry\"}", "{\"start\":52.17,\"end\":57.27,\"transcript\":\"supported and sourced curriculum. Itfocuses on the key specialist skills\"}", "{\"start\":57.27,\"end\":62.969,\"transcript\":\"required for profession in the ITindustry. Your first year will provide\"}", "{\"start\":62.969,\"end\":68.67,\"transcript\":\"you with a strong industry background,the skills, programming languages, and\"}", "{\"start\":68.67,\"end\":73.92,\"transcript\":\"software that you'll be exposed to willassist with designing and building the\"}", "{\"start\":73.92,\"end\":79.65,\"transcript\":\"foundation for subsequent year levels.For example you'll experience Python and\"}", "{\"start\":79.65,\"end\":84.6,\"transcript\":\"Java programming languages and learn howto problem solve for challenging\"}", "{\"start\":84.6,\"end\":90.18,\"transcript\":\"programming and user context. In yoursecond year you'll begin to specialise\"}", "{\"start\":90.18,\"end\":95.31,\"transcript\":\"in data analytics using data sciencetools and activities while at the same\"}", "{\"start\":95.31,\"end\":101.13,\"transcript\":\"time further developing your skills inprogramming. You'll be introduced to the\"}", "{\"start\":101.13,\"end\":106.079,\"transcript\":\"discipline areas of systems design, webdevelopment, and interface design while\"}", "{\"start\":106.079,\"end\":111.45,\"transcript\":\"continuing to develop your programmingand analytical skills as well as your\"}", "{\"start\":111.45,\"end\":117.09,\"transcript\":\"knowledge base. Your third year gives youthe opportunity to undertake a complex\"}", "{\"start\":117.09,\"end\":122.1,\"transcript\":\"IT project relevant to industry. You'll workclosely with your online course\"}", "{\"start\":122.1,\"end\":126.49,\"transcript\":\"facilitator and tutors and practiceand develop the skills acquired\"}", "{\"start\":126.49,\"end\":131.17,\"transcript\":\"throughout your degree.In addition to the hard technical skills\"}", "{\"start\":131.17,\"end\":135.07,\"transcript\":\"that you'll perfect throughout yourdegree you'll also experience social,\"}", "{\"start\":135.07,\"end\":140.08,\"transcript\":\"ethical, and professional issues relatedto the discipline as well as complete a\"}", "{\"start\":140.08,\"end\":145.78,\"transcript\":\"group project. You'll gain a broad levelof experience. The projects you undertake\"}", "{\"start\":145.78,\"end\":150.01,\"transcript\":\"will develop your data analysis,graphical, report writing, and\"}", "{\"start\":150.01,\"end\":155.29,\"transcript\":\"presentation skills. These learningexperiences will allow you to develop a\"}", "{\"start\":155.29,\"end\":159.61,\"transcript\":\"working knowledge to overcome thechallenges of designing, developing, and\"}", "{\"start\":159.61,\"end\":166.209,\"transcript\":\"evaluating real world IT systems. Our aimis to ensure that you are successful in\"}", "{\"start\":166.209,\"end\":172.209,\"transcript\":\"your studies and that you become a partof UniSA's rich history of producing\"}", "{\"start\":172.209,\"end\":177.73,\"transcript\":\"outstanding graduates. The teaching teamis very excited and keen to meet and\"}", "{\"start\":177.73,\"end\":182.62,\"transcript\":\"work with you throughout your degree.Best wishes for your studies with UniSA Online.\"}"],
                        },
                    ]
                }
                </pre>

                <pre>
                {
                    "identifiers": [
                        "MKoroBS8Ke4",
                        "PxXp5ZgtbLQ"
                    ]
                }
                </pre>
            </div><!-- space -->






        </div><!-- admin-page-section -->

    </div><!-- container-fluid -->

@endsection


@section('javascript')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.5/validator.min.js"></script>
@endsection
