<?php
declare(strict_types = 1);

namespace Civi\Api4\Action\GmailConnect;

use Civi\Api4\Generic\AbstractAction;
use Civi\Api4\Generic\Result;
use Civi\Gmailconnect\Helper;

/**
 * Find a contact by email
 *
 * Matches contact email addresses and returns the first matching
 * contact or an empty result if none is found
 *
 * @package gmailconnect
 */
class GetContact extends AbstractAction {

  /**
   * Email address
   *
   * @var string
   * @required
   */
  protected $email;

  public function _run(Result $result): void {
    $email = trim((string) $this->email);
    if ($email === '') {
      throw new \CRM_Core_Exception('email is required.');
    }

    $contact = Helper::findContactByEmail($email);
    if ($contact) {
      $result[] = [
        'id' => (int) $contact['id'],
        'display_name' => $contact['display_name'],
        'url' => Helper::contactUrl((int) $contact['id']),
      ];
    }
  }

}
