<?php
declare(strict_types = 1);

use Civi\Gmailconnect\Helper;

/**
 * Single endpoint called by the CiviCRM Connect Gmail add-on
 *
 * POST with a JSON body {"action": "...", "params": {...}} and the contact's
 * token in the X-Gmail-Connect-Token header (or a token query parameter).
 * The action runs as the token's contact. Responds with {"values": [...]}
 * or {"error": "..."}.
 */
class CRM_Gmailconnect_Page_Endpoint extends CRM_Core_Page {

  private const ACTIONS = ['recordActivity', 'getActivity', 'addContact', 'getContact', 'lookup'];

  public function run() {
    [$status, $response] = $this->handle();
    http_response_code($status);
    CRM_Utils_JSON::output($response);
  }

  /**
   * @return array [HTTP status, response body]
   */
  private function handle(): array {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      return [405, ['error' => 'Use POST.']];
    }

    $token = $_SERVER['HTTP_X_GMAIL_CONNECT_TOKEN'] ?? $_GET['token'] ?? '';
    $contactId = Helper::findContactIdByToken((string) $token);
    if (!$contactId) {
      return [401, ['error' => 'Invalid token.']];
    }
    // Without a linked CMS user the permission check would fall back to the
    // anonymous user
    if (!CRM_Core_BAO_UFMatch::getUFId($contactId) || !CRM_Core_Permission::check('access Gmail Connect endpoints', $contactId)) {
      return [403, ['error' => 'You do not have permission to use Gmail Connect.']];
    }

    $request = json_decode(file_get_contents('php://input'), TRUE);
    $action = $request['action'] ?? NULL;
    $params = $request['params'] ?? [];
    if (!in_array($action, self::ACTIONS, TRUE) || !is_array($params)) {
      return [400, ['error' => 'Unknown action or invalid params.']];
    }

    // Run as the token's contact for this request only, without a session
    // that could be reused
    CRM_Core_Session::useFakeSession();
    CRM_Core_Session::singleton()->set('userID', $contactId);

    try {
      $params['checkPermissions'] = FALSE;
      return [200, ['values' => (array) civicrm_api4('GmailConnect', $action, $params)]];
    }
    catch (\Civi\Core\Exception\DBQueryException $e) {
      return $this->serverError($e);
    }
    catch (CRM_Core_Exception $e) {
      return [400, ['error' => $e->getMessage()]];
    }
    catch (\Throwable $e) {
      return $this->serverError($e);
    }
  }

  private function serverError(\Throwable $e): array {
    $errorId = CRM_Core_Error::createErrorId();
    Civi::log()->error("Gmail Connect endpoint error ($errorId): " . $e->getMessage(), ['exception' => $e]);
    return [500, ['error' => "Sorry, an error occurred (Error ID: $errorId)."]];
  }

}
