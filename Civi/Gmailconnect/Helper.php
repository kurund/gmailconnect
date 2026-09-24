<?php
declare(strict_types = 1);

namespace Civi\Gmailconnect;

/**
 * Helper functions for the Gmail Connect extension
 */
class Helper {

  /**
   * Activity type used for emails logged using this extension
   */
  public const ACTIVITY_TYPE = 'External_Email';

  /**
   * Custom field for storing RFC 822 Message-ID header
   */
  public const MESSAGE_ID_FIELD = 'External_Email_Details.MessageID';

  /**
   * Find the first contact with the matching email
   *
   * @param string $email
   *
   * @return array|null Contact with id and display_name or NULL if not found
   */
  public static function findContactByEmail(string $email): ?array {
    return \Civi\Api4\Contact::get(FALSE)
      ->addSelect('id', 'display_name')
      ->addJoin('Email AS email', 'INNER', ['email.contact_id', '=', 'id'])
      ->addWhere('email.email', '=', $email)
      ->addWhere('is_deleted', '=', FALSE)
      ->addOrderBy('id', 'ASC')
      ->setLimit(1)
      ->execute()
      ->first();
  }

  /**
   * Parse email address
   * Email might be "Jane Doe <jane@doe.com>" or "jane@doe.com"
   *
   * @param string $address
   *
   * @return array|null
   *   ['email' => string, 'name' => array] with name fields first_name,
   *   middle_name, last_name or NULL if the email is invalid
   */
  public static function parseAddress(string $address): ?array {
    $parsed = \ezcMailTools::parseEmailAddress(trim($address));
    if (!$parsed || !\CRM_Utils_Rule::email($parsed->email)) {
      return NULL;
    }

    $name = [];
    // Some clients repeat the email as the name
    if (strcasecmp(trim($parsed->name), $parsed->email) !== 0) {
      \CRM_Utils_String::extractName($parsed->name, $name);
    }
    return ['email' => $parsed->email, 'name' => $name];
  }

  /**
   * Find the first contact with the matching email or create an
   * Individual with it as their primary email
   *
   * @param string $email
   * @param array $name Name fields for a new contact: first_name, middle_name, last_name
   *
   * @return array ['id' => int, 'created' => bool]
   */
  public static function findOrCreateContactByEmail(string $email, array $name = []): array {
    $contactId = self::findContactByEmail($email)['id'] ?? NULL;
    if ($contactId) {
      return ['id' => $contactId, 'created' => FALSE];
    }

    $contactId = \Civi\Api4\Contact::create(FALSE)
      ->setValues([
        'contact_type' => 'Individual',
        'first_name' => $name['first_name'] ?? '',
        'middle_name' => $name['middle_name'] ?? '',
        'last_name' => $name['last_name'] ?? '',
        'email_primary.email' => $email,
      ])
      ->execute()
      ->first()['id'];
    return ['id' => $contactId, 'created' => TRUE];
  }

  /**
   * Acquire the lock shared by all write endpoints, so concurrent requests
   * cannot create duplicate activities or contacts. Throws if it cannot be
   * acquired within 10 seconds
   */
  public static function lock(): \Civi\Core\Lock\LockInterface {
    $lock = \Civi::lockManager()->acquire('data.gmailconnect.write', 10);
    if (!$lock->isAcquired()) {
      throw new \CRM_Core_Exception('Could not acquire Gmail Connect lock, please retry.');
    }
    return $lock;
  }

  /**
   * Find the External Email activity with the given MessageID
   *
   * @param string $messageId
   *
   * @return int|null
   */
  public static function findActivityIdByMessageId(string $messageId): ?int {
    return \Civi\Api4\Activity::get(FALSE)
      ->addSelect('id')
      ->addWhere('activity_type_id:name', '=', self::ACTIVITY_TYPE)
      ->addWhere(self::MESSAGE_ID_FIELD, '=', $messageId)
      ->addWhere('is_deleted', '=', FALSE)
      ->addOrderBy('id', 'ASC')
      ->setLimit(1)
      ->execute()
      ->first()['id'] ?? NULL;
  }

  /**
   * The contact's token, created on first use
   */
  public static function getToken(int $contactId): string {
    $token = \Civi\Api4\GmailConnectToken::get(FALSE)
      ->addSelect('token')
      ->addWhere('contact_id', '=', $contactId)
      ->execute()
      ->first()['token'] ?? NULL;
    return $token ?? self::regenerateToken($contactId);
  }

  /**
   * Replace the contact's token with a new one, so the old one stops working
   */
  public static function regenerateToken(int $contactId): string {
    $token = \CRM_Utils_String::createRandom(64, \CRM_Utils_String::ALPHANUMERIC);
    \Civi\Api4\GmailConnectToken::save(FALSE)
      ->addRecord(['contact_id' => $contactId, 'token' => $token])
      ->setMatch(['contact_id'])
      ->execute();
    return $token;
  }

  /**
   * Contact the token belongs to, or NULL if the token is unknown
   */
  public static function findContactIdByToken(string $token): ?int {
    if (empty($token)) {
      return NULL;
    }
    return \Civi\Api4\GmailConnectToken::get(FALSE)
      ->addSelect('contact_id')
      ->addWhere('token', '=', $token)
      ->execute()
      ->first()['contact_id'] ?? NULL;
  }

  /**
   * The contact's personal endpoint URL, to copy into the Gmail add-on
   */
  public static function endpointUrl(string $token): string {
    return (string) \Civi::url('frontend://civicrm/gmailconnect', 'a')
      ->addQuery(['token' => $token]);
  }

  /**
   * Absolute URL to view an activity in CiviCRM
   */
  public static function activityUrl(int $activityId): string {
    return (string) \Civi::url('backend://civicrm/activity', 'a')
      ->addQuery(['action' => 'view', 'reset' => 1, 'id' => $activityId]);
  }

  /**
   * Absolute URL to view a contact in CiviCRM
   */
  public static function contactUrl(int $contactId): string {
    return (string) \Civi::url('backend://civicrm/contact/view', 'a')
      ->addQuery(['reset' => 1, 'cid' => $contactId]);
  }

}
