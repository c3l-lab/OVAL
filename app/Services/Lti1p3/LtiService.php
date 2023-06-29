<?php
namespace oval\Services\Lti1p3;

use Illuminate\Http\Request;
use oval\LtiRegistration;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ICookie;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\JwksEndpoint;
use Packback\Lti1p3\LtiDeepLinkResource;
use Packback\Lti1p3\LtiMessageLaunch;
use Packback\Lti1p3\LtiOidcLogin;

const LTI_PASSWORD = '[lti_password]';

class LtiService
{
  public $db;
  public $cache;
  public $cookie;
  public $serviceConnector;
  private $launchUrl;

  public function __construct(
    IDatabase $db,
    ICache $cache,
    ICookie $cookie,
    ILtiServiceConnector $serviceConnector
  ) {
    $this->db = $db;
    $this->cache = $cache;
    $this->cookie = $cookie;
    $this->serviceConnector = $serviceConnector;


    $this->launchUrl = route('lti1p3.launch');
  }

  /**
   * Validate an LTI launch.
   *
   * @throws Packback\Lti1p3\LtiException
   */
  public function validateLaunch(Request $request): LtiMessageLaunch
  {
    return LtiMessageLaunch::new($this->db, $this->cache, $this->cookie, $this->serviceConnector)
      ->validate($request->all());
  }

  /**
   * Launch a deep link.
   */
  public function launchDeepLink(LtiMessageLaunch $launch): void
  {
    $resource = LtiDeepLinkResource::new()
      ->setUrl($this->launchUrl);
    $launch->getDeepLink()->outputResponseForm([$resource]);
  }

  /**
   * Get the URL for an OIDC login redirect.
   *
   * @throws Packback\Lti1p3\OidcException
   */
  public function login(Request $request): string
  {
    return LtiOidcLogin::new($this->db, $this->cache, $this->cookie)
      ->doOidcLoginRedirect($this->launchUrl, $request->all())
      ->getRedirectUrl();
  }

  /**
   * Get a JWKS objects (optionally by ID).
   */
  public function jwks(string $id = null): array
  {
    $registrations = LtiRegistration::all();
    $keys = $registrations->reduce(function ($acc, $registration) {
      $acc[$registration->key_id] = $registration->getPrivateKey();
      return $acc;
    }, []);

    return JwksEndpoint::new($keys)->getPublicJwks();
  }
}
