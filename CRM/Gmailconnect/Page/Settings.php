<?php
declare(strict_types = 1);

use Civi\Gmailconnect\Helper;
use CRM_Gmailconnect_ExtensionUtil as E;

/**
 * Shows the Gmail Connect contact its API key and the endpoint
 * details which can be used to configure the CiviCRM Connect Gmail add-on
 */
class CRM_Gmailconnect_Page_Settings extends CRM_Core_Page {

  public function run() {
    CRM_Utils_System::setTitle(E::ts('Gmail Connect Settings'));

    $contactId = Civi::settings()->get('gmailconnect_contact_id');
    $contact = NULL;
    if ($contactId) {
      $contact = \Civi\Api4\Contact::get(FALSE)
        ->addSelect('id', 'display_name', 'is_deleted', 'api_key')
        ->addWhere('id', '=', $contactId)
        ->execute()
        ->first();
    }

    if ($contact) {
      $ufId = CRM_Core_BAO_UFMatch::getUFId($contactId);
      $user = $ufId ? get_userdata($ufId) : NULL;
      $contact['url'] = Helper::contactUrl($contactId);
      $contact['user_login'] = $user->user_login ?? NULL;
      $contact['user_email'] = $user->user_email ?? NULL;
    }
    $this->assign('contact', $contact);

    $this->assign('siteUrl', rtrim(CRM_Core_Config::singleton()->userFrameworkBaseURL, '/'));

    parent::run();
  }

}
