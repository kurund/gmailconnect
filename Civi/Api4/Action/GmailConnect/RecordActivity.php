<?php
declare(strict_types = 1);

namespace Civi\Api4\Action\GmailConnect;

use Civi\Api4\Activity;
use Civi\Api4\Generic\AbstractAction;
use Civi\Api4\Generic\Result;
use Civi\Gmailconnect\Helper;

/**
 * Log an email as an External Email activity
 *
 * The sender becomes the source contact and the to, cc and bcc recipients
 * become target contacts. Contacts are matched on any of their emails and
 * created as individuals when not found.
 *
 * If an activity already exists for the Message-ID, nothing is created and
 * the existing activity is returned with created = FALSE.
 *
 * @package gmailconnect
 */
class RecordActivity extends AbstractAction {

  /**
   * Email subject
   *
   * @var string
   */
  protected $subject = '';

  /**
   * Email body (HTML)
   *
   * @var string
   */
  protected $details = '';

  /**
   * Sender address, e.g. "Jane Doe <jane@doe.com>" or "jane@doe.com"
   *
   * @var string
   * @required
   */
  protected $from;

  /**
   * To recipient addresses, in the same format as from
   *
   * @var array
   */
  protected $to = [];

  /**
   * Cc recipient addresses, in the same format as from
   *
   * @var array
   */
  protected $cc = [];

  /**
   * Bcc recipient addresses, in the same format as from
   *
   * @var array
   */
  protected $bcc = [];

  /**
   * RFC 822 Message-ID header of the email
   *
   * @var string
   * @required
   */
  protected $messageId;

  public function _run(Result $result): void {
    $messageId = trim($this->messageId);
    if (empty($messageId)) {
      throw new \CRM_Core_Exception('messageId is required.');
    }
    if (mb_strlen($messageId) > 255) {
      throw new \CRM_Core_Exception('messageId must be at most 255 characters.');
    }

    [$from, $targets] = $this->parseAddresses();

    // use lock to prevent duplicate creation
    $lock = Helper::lock();
    try {
      $activityId = Helper::findActivityIdByMessageId($messageId);
      $created = FALSE;

      if (!$activityId) {
        // add transaction as we are doing mutiple things here
        $transaction = new \CRM_Core_Transaction();
        try {
          $sourceContactId = Helper::findOrCreateContactByEmail($from['email'], $from['name'])['id'];
          $targetContactIds = [];
          foreach ($targets as $target) {
            $targetContactIds[] = Helper::findOrCreateContactByEmail($target['email'], $target['name'])['id'];
          }

          $activityId = Activity::create(FALSE)
            ->setValues([
              'activity_type_id:name' => Helper::ACTIVITY_TYPE,
              'status_id:name' => 'Completed',
              'activity_date_time' => date('Y-m-d H:i:s'),
              'subject' => mb_substr($this->subject, 0, 255),
              'details' => $this->details,
              'source_contact_id' => $sourceContactId,
              'target_contact_id' => array_values(array_unique($targetContactIds)),
              Helper::MESSAGE_ID_FIELD => $messageId,
            ])
            ->execute()
            ->first()['id'];
        }
        catch (\Throwable $e) {
          $transaction->rollback()->commit();
          throw $e;
        }
        $transaction->commit();
        $created = TRUE;
      }
    }
    finally {
      $lock->release();
    }

    $result[] = [
      'id' => $activityId,
      'url' => Helper::activityUrl($activityId),
      'created' => $created,
    ];
  }

  /**
   * Parse the from, to, cc and bcc addresses.
   *
   * Addresses are parsed with the CiviCRM core email parser and names split with
   * CRM_Utils_String::extractName() like the core email processor.
   * Targets are de-duped by email
   *
   * @return array [$from, $targets], each address as ['email' => string, 'name' => array]
   */
  private function parseAddresses(): array {
    $addresses = [['from', $this->from]];
    foreach (['to', 'cc', 'bcc'] as $param) {
      foreach ($this->$param as $address) {
        $addresses[] = [$param, $address];
      }
    }

    $from = NULL;
    $targets = [];
    $invalid = [];
    foreach ($addresses as [$param, $address]) {
      if (!is_string($address)) {
        throw new \CRM_Core_Exception("$param must contain email addresses.");
      }
      if ($param !== 'from' && empty(trim($address))) {
        continue;
      }

      $parsed = Helper::parseAddress($address);
      if (!$parsed) {
        $invalid[] = $address;
        continue;
      }

      if ($param === 'from') {
        $from = $parsed;
      }
      else {
        $key = strtolower($parsed['email']);
        if (!isset($targets[$key])) {
          $targets[$key] = $parsed;
        }
      }
    }

    if ($invalid) {
      throw new \CRM_Core_Exception('Invalid email(s): ' . implode(', ', $invalid));
    }
    return [$from, array_values($targets)];
  }

}
