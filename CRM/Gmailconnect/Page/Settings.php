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

    $contactId = (int) Civi::settings()->get('gmailconnect_contact_id');
    $contact = NULL;
    if ($contactId) {
      $contact = \Civi\Api4\Contact::get(FALSE)
        ->addSelect('id', 'display_name', 'is_deleted')
        ->addWhere('id', '=', $contactId)
        ->execute()
        ->first();
    }

    if ($contact) {
      $ufId = CRM_Core_BAO_UFMatch::getUFId($contactId);
      $user = $ufId ? get_userdata($ufId) : NULL;
      $this->assign('contact', [
        'id' => $contactId,
        'display_name' => $contact['display_name'],
        'is_deleted' => (bool) $contact['is_deleted'],
        'url' => Helper::contactUrl($contactId),
        'api_key' => CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_Contact', $contactId, 'api_key'),
        'user_login' => $user ? $user->user_login : NULL,
        'user_email' => $user ? $user->user_email : NULL,
      ]);
    }
    else {
      $this->assign('contact', NULL);
    }

    $this->assign('endpointUrl', (string) Civi::url('frontend://civicrm/ajax/api4/GmailConnect', 'a'));

    parent::run();
  }

}
