<div class="admin-page-section-header">
    <h2>{{ isset($deployment) ? "EDIT" : "ADD NEW "}} PLATFORM</h2>
</div><!-- admin-page-section-header -->

<div class="admin-page-section">
    <div class="space-left-right">

        <form
          id="add-lti-form"
          method="POST"
          action="{{ isset($deployment) ? "/lti/deployments/{$deployment->id}" : "/lti/deployments" }}"
          role="form"
          data-toggle="validator"
        >
            @isset($deployment)
              @method('PUT')
            @endisset
            @csrf
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="deployment-name">Name</label>
                        <input type="text" id="deployment-name" class="form-control gray-textbox"
                            name="name" value="{{ isset($deployment) ? $deployment->name : "" }}">
                    </div><!-- form-group -->

                    <div class="form-group">
                        <label for="deployment-client-id">Client ID</label>
                        <input type="text" id="deployment-client-id" class="form-control gray-textbox"
                            name="client_id" value="{{ isset($deployment) ? $deployment->client_id : "" }}">
                    </div><!-- form-group -->

                    <div class="form-group">
                        <label for="deployment-deployment-id">Deployment ID</label>
                        <input type="text" id="deployment-deployment-id" class="form-control gray-textbox"
                            name="deployment_id" value="{{ isset($deployment) ? $deployment->deployment_id : "" }}">
                    </div><!-- form-group -->

                    <div class="form-group">
                        <label for="deployment-keyset-url">Keyset URL</label>
                        <input type="text" id="deployment-keyset-url" class="form-control gray-textbox"
                            name="keyset_url" value="{{ isset($deployment) ? $deployment->keyset_url : "" }}">
                    </div><!-- form-group -->

                    <div class="form-group">
                        <label for="deployment-auth-token-url">Access Token URL</label>
                        <input type="text" id="deployment-auth-token-url" class="form-control gray-textbox"
                            name="auth_token_url" value="{{ isset($deployment) ? $deployment->auth_token_url : "" }}">
                    </div><!-- form-group -->

                    <div class="form-group">
                        <label for="deployment-auth-login-url">Login URL</label>
                        <input type="text" id="deployment-auth-login-url" class="form-control gray-textbox"
                            name="auth_login_url" value="{{ isset($deployment) ? $deployment->auth_login_url : "" }}">
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
