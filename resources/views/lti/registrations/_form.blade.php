<div class="admin-page-section-header">
    <h2>{{ isset($registration) ? "EDIT" : "ADD NEW "}} REGISTRATIONS</h2>
</div><!-- admin-page-section-header -->

<div class="admin-page-section">
    <div class="space-left-right">

        <form
          id="add-lti-form"
          method="POST"
          action="{{ isset($registration) ? "/lti/registrations/{$registration->id}" : "/lti/registrations" }}"
          role="form"
          data-toggle="validator"
        >
            @isset($registration)
              @method('PUT')
            @endisset
            @csrf
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="registration-name">Name</label>
                        <input type="text" id="lti-registration-name" class="form-control gray-textbox"
                            name="name" required value="{{ isset($registration) ? $registration->name : "" }}">
                    </div><!-- form-group -->

                    <div class="form-group">
                        <label for="registration-issuer">Issuer</label>
                        <input type="text" id="lti-registration-issuer" class="form-control gray-textbox"
                            name="issuer" required placeholder="https://lift.c3l.ai" value="{{ isset($registration) ? $registration->issuer : "" }}">
                    </div><!-- form-group -->

                          <div class="form-group">
                        <label for="registration-client-id">Client ID</label>
                        <input type="text" id="registration-client-id" class="form-control gray-textbox"
                            name="client_id" required value="{{ isset($registration) ? $registration->client_id : "" }}">
                    </div><!-- form-group -->

                    <div class="form-group">
                        <label for="registration-registration-id">Deployment ID</label>
                        <input type="text" id="registration-registration-id" class="form-control gray-textbox"
                            name="deployment_id" required value="{{ isset($registration) ? $registration->deployment_id : "" }}">
                    </div><!-- form-group -->

                    <div class="form-group">
                        <label for="registration-keyset-url">Keyset URL</label>
                        <input type="text" id="registration-keyset-url" class="form-control gray-textbox"
                            name="keyset_url" required value="{{ isset($registration) ? $registration->keyset_url : "" }}">
                    </div><!-- form-group -->

                    <div class="form-group">
                        <label for="registration-auth-token-url">Access Token URL</label>
                        <input type="text" id="registration-auth-token-url" class="form-control gray-textbox"
                            name="auth_token_url" value="{{ isset($registration) ? $registration->auth_token_url : "" }}">
                    </div><!-- form-group -->

                    <div class="form-group">
                        <label for="registration-auth-login-url">Login URL</label>
                        <input type="text" id="registration-auth-login-url" class="form-control gray-textbox"
                            name="auth_login_url" required value="{{ isset($registration) ? $registration->auth_login_url : "" }}">
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
