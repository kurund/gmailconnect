<?php
declare(strict_types = 1);

namespace Civi\Api4\Action\GmailConnect;

use Civi\Api4\Generic\AbstractAction;
use Civi\Api4\Generic\Result;
use Civi\Gmailconnect\Helper;

/**
 * Create an individual with the given email
 *
 * If contact already exist with the email nothing is created and
 * the first matching contact is returned with created = FALSE
 *
 * @package gmailconnect
 */
class AddContact extends AbstractAction {

  /**
   * First name
   *
   * @var string
   */
  protected $firstName = '';

  /**
   * Last name
   *
   * @var string
   */
  protected $lastName = '';

  /**
   * Email address
   *
   * @var string
   * @required
   */
  protected $email;

  public function _run(Result $result): void {
    $email = trim((string) $this->email);
    if (!\CRM_Utils_Rule::email($email)) {
      throw new \CRM_Core_Exception('A valid email is required.');
    }

    // add lock to prevent duplicate contact creation
    $lock = Helper::lock();
    try {
      $contact = Helper::findOrCreateContactByEmail($email, [
        'first_name' => trim((string) $this->firstName),
        'last_name' => trim((string) $this->lastName),
      ]);
    }
    finally {
      $lock->release();
    }

    $result[] = $contact;
  }

}
