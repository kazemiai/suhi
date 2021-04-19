
<?php

namespace Drupal\multistep\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * A controller that redirects to Form Closed page.
 */
class MultistepController {

  /**
   * Redirect to
   */
  public function deactivateForm() {
   // $base_url = 'https://kazemicode.org/suhi';

   // $path = '/courses/closed';

   // $response = new RedirectResponse($base_url . $path);
  //  $response->send();
    return new Response('Preregistration is now closed!');
  }
  



}
