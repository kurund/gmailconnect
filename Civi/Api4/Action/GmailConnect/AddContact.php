<?php
declare(strict_types = 1);

namespace Civi\Api4\Action\GmailConnect;

use Civi\Api4\Generic\AbstractAction;
use Civi\Api4\Generic\Result;
use Civi\Gmailconnect\Helper;

/**
 * Create an individual with the given email
 *
 * The email may include a name e.g. "Jane Doe <jane@doe.com>" which is
 * used for the new contact unless firstName or lastName is given
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
   * Email address e.g. "Jane Doe <jane@doe.com>" or "jane@doe.com"
   *
   * @var string
   * @required
   */
  protected $email;

  public function _run(Result $result): void {
    $address = Helper::parseAddress($this->email);
    if (!$address) {
      throw new \CRM_Core_Exception('A valid email is required.');
    }

    $name = $address['name'];
    $firstName = trim($this->firstName);
    $lastName = trim($this->lastName);
    if (!empty($firstName) || !empty($lastName)) {
      $name = ['first_name' => $firstName, 'last_name' => $lastName];
    }

    $lock = Helper::lock();
    try {
      $contact = Helper::findOrCreateContactByEmail($address['email'], $name);
    }
    finally {
      $lock->release();
    }

    $result[] = $contact;
  }

}
