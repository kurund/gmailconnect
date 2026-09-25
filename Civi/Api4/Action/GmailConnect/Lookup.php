<?php
declare(strict_types = 1);

namespace Civi\Api4\Action\GmailConnect;

use Civi\Api4\Generic\AbstractAction;
use Civi\Api4\Generic\Result;
use Civi\Gmailconnect\Helper;

/**
 * Everything the add-on shows for an opened email in one call: whether the
 * email is already recorded and the contact for each email address
 *
 * Returns one row: activity ({id, url} or NULL), contacts (lowercased
 * email => {id, display_name, url}, only for emails that match a contact) and
 * user ({id, display_name} of the contact making the request, or NULL)
 *
 * @package gmailconnect
 */
class Lookup extends AbstractAction {

  /**
   * RFC 822 Message-ID header of the email
   *
   * @var string
   */
  protected $messageId = '';

  /**
   * Email addresses of the email's participants
   *
   * @var array
   */
  protected $emails = [];

  public function _run(Result $result): void {
    $activity = NULL;
    $activityId = empty(trim($this->messageId)) ? NULL : Helper::findActivityIdByMessageId(trim($this->messageId));
    if ($activityId) {
      $activity = ['id' => $activityId, 'url' => Helper::activityUrl($activityId)];
    }

    $contacts = [];
    foreach ($this->emails as $email) {
      $contact = Helper::findContactByEmail(trim($email));
      if ($contact) {
        $contacts[strtolower(trim($email))] = [
          'id' => $contact['id'],
          'display_name' => $contact['display_name'],
          'url' => Helper::contactUrl($contact['id']),
        ];
      }
    }

    $user = NULL;
    $userId = \CRM_Core_Session::getLoggedInContactID();
    if ($userId) {
      $user = ['id' => $userId, 'display_name' => \CRM_Contact_BAO_Contact::displayName($userId)];
    }

    $result[] = ['activity' => $activity, 'contacts' => $contacts, 'user' => $user];
  }

}
