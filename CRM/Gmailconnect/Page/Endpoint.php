<?php
declare(strict_types = 1);

use Civi\Gmailconnect\Helper;

/**
 * Single endpoint called by the CiviCRM Connect Gmail add-on
 *
 */
class CRM_Gmailconnect_Page_Endpoint extends CRM_Core_Page {

  private const ACTIONS = ['recordActivity', 'getActivity', 'addContact', 'getContact', 'lookup'];

  public function run() {
    [$status, $response] = $this->handle();
    http_response_code($status);
    CRM_Utils_JSON::output($response);
  }

  /**
   * Gmail add-on expects status code so always return status code and body
   *
   * @return array [HTTP status, response body]
   */
  private function handle(): array {
    $token = $_SERVER['HTTP_X_GMAIL_CONNECT_TOKEN'] ?? $_GET['token'] ?? '';

    if (empty($token)) {
      return [401, ['error' => 'Invalid token.']];
    }

    $contactId = Helper::findContactIdByToken($token);
    if (!$contactId) {
      return [401, ['error' => 'Invalid token.']];
    }

    if (!CRM_Core_BAO_UFMatch::getUFId($contactId) || !CRM_Core_Permission::check('access Gmail Connect endpoints', $contactId)) {
      return [403, ['error' => 'You do not have permission to use Gmail Connect.']];
    }

    $request = json_decode(file_get_contents('php://input'), TRUE);
    $action = $request['action'] ?? NULL;
    $params = $request['params'] ?? [];
    if (!in_array($action, self::ACTIONS, TRUE) || !is_array($params)) {
      return [400, ['error' => 'Unknown action or invalid params.']];
    }

    CRM_Core_Session::useFakeSession();
    CRM_Core_Session::singleton()->set('userID', $contactId);

    try {
      $params['checkPermissions'] = FALSE;
      return [200, ['values' => civicrm_api4('GmailConnect', $action, $params)->getArrayCopy()]];
    }
    catch (CRM_Core_Exception $e) {
      return [400, ['error' => $e->getMessage()]];
    }
    catch (\Throwable $e) {
      return [500, ['error' => "Sorry, an error occurred please contact your CiviCRM adminstrator."]];
    }
  }

}
