<div class="admin-page-section-header">
    <h2>{{ isset($platform) ? "EDIT" : "ADD NEW "}} PLATFORM</h2>
</div><!-- admin-page-section-header -->

<div class="admin-page-section">
    <div class="space-left-right">

        <form
          id="add-lti-form"
          method="POST"
          action="{{ isset($platform) ? "/lti/platforms/{$platform->id}" : "/lti/platforms" }}"
          role="form"
          data-toggle="validator"
        >
            @isset($platform)
              @method('PUT')
            @endisset
            @csrf
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="lti-connection-name">Name</label>
                        <input type="text" id="lti-connection-name" class="form-control gray-textbox"
                            name="name" value="{{ isset($platform) ? $platform->name : "" }}">
                    </div><!-- form-group -->

                    <div class="form-group">
                        <label for="lti-connection-issuer">Issuer</label>
                        <input type="text" id="lti-connection-issuer" class="form-control gray-textbox"
                            name="iss" placeholder="https://lift.c3l.ai" value="{{ isset($platform) ? $platform->iss : "" }}">
                    </div><!-- form-group -->
                </div><!-- col -->
            </div><!-- row -->

            <button type="submit" class="rectangle-button btn">
                SAVE
                <i class="fa fa-floppy-o" aria-hidden="true"></i>
            </button>

        </form>
    </div><!-- space-left-right -->
</div><!-- admin-page-section -->
