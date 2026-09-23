<?php
declare(strict_types = 1);

use Civi\Gmailconnect\Helper;
use CRM_Gmailconnect_ExtensionUtil as E;

/**
 * Sets up the Gmail Connect role and user on install
 *
 * Uninstall intentionally does not delete any data
 */
class CRM_Gmailconnect_Upgrader extends CRM_Extension_Upgrader_Base {

  /**
   * WordPress capabilities granted to the Gmail Connect role
   */
  private const ROLE_CAPABILITIES = [
    'read' => TRUE,
    'access_gmail_connect_endpoints' => TRUE,
    'authenticate_with_api_key' => TRUE,
  ];

  public function postInstall(): void {
    if (CIVICRM_UF !== 'WordPress') {
      CRM_Core_Session::setStatus(
        E::ts('Gmail Connect only creates its role and user on WordPress. Please set them up manually.'),
        E::ts('Gmail Connect'),
        'alert'
      );
      return;
    }

    $this->createRole();
    $userId = $this->findOrCreateUser();
    $contactId = $this->getContactId($userId);
    $apiKey = $this->ensureApiKey($contactId);
    Civi::settings()->set('gmailconnect_contact_id', $contactId);

    CRM_Core_Session::setStatus(
      E::ts('The Gmail Connect user (contact ID %1) was set up with API key: <code>%2</code>. You can view it again under Administer &raquo; System Settings &raquo; Gmail Connect Settings.', [
        1 => $contactId,
        2 => $apiKey,
      ]),
      E::ts('Gmail Connect installed'),
      'success',
      ['expires' => 0]
    );
  }

  /**
   * Create the Gmail Connect role or make sure an existing one has the
   * required capabilities
   */
  private function createRole(): void {
    $role = get_role(Helper::ROLE);
    if (!$role) {
      add_role(Helper::ROLE, 'Gmail Connect', self::ROLE_CAPABILITIES);
      return;
    }
    foreach (self::ROLE_CAPABILITIES as $capability => $grant) {
      $role->add_cap($capability, $grant);
    }
  }

  /**
   * Reuse the existing Gmail Connect user or create a new one
   *
   * @return int WordPress user ID
   */
  private function findOrCreateUser(): int {
    $existing = get_users([
      'role' => Helper::ROLE,
      'orderby' => 'ID',
      'order' => 'ASC',
      'number' => 1,
      'fields' => 'ID',
    ]);
    if ($existing) {
      return (int) $existing[0];
    }

    $hash = bin2hex(random_bytes(4));

    $userId = wp_insert_user([
      'user_login' => 'gmailconnect-' . $hash,
      'user_email' => 'gmailconnect+' . $hash . '@example.com',
      'user_pass' => wp_generate_password(64, TRUE, TRUE),
      'first_name' => 'Gmail',
      'last_name' => 'Connect',
      'display_name' => 'Gmail Connect',
      'role' => Helper::ROLE,
    ]);

    if (is_wp_error($userId)) {
      throw new CRM_Core_Exception('Gmail Connect: could not create WordPress user: ' . $userId->get_error_message());
    }
    return $userId;
  }

  /**
   * Get the contact linked to the WordPress user syncing it if needed
   */
  private function getContactId(int $userId): int {
    $contactId = CRM_Core_BAO_UFMatch::getContactId($userId);
    if (!$contactId) {
      $user = get_userdata($userId);
      CRM_Core_BAO_UFMatch::synchronizeUFMatch($user, $userId, $user->user_email, 'WordPress', NULL, 'Individual');
      $contactId = CRM_Core_BAO_UFMatch::getContactId($userId);
    }
    if (!$contactId) {
      throw new CRM_Core_Exception('Gmail Connect: could not find the contact for WordPress user ' . $userId);
    }
    return $contactId;
  }

  /**
   * Return the contact's API key and generating new if does not exist
   */
  private function ensureApiKey(int $contactId): string {
    $apiKey = CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_Contact', $contactId, 'api_key');
    if (!$apiKey) {
      $apiKey = CRM_Utils_String::createRandom(32, CRM_Utils_String::ALPHANUMERIC);
      CRM_Core_DAO::setFieldValue('CRM_Contact_DAO_Contact', $contactId, 'api_key', $apiKey);
    }
    return $apiKey;
  }

}
