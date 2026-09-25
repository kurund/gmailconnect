<?php
declare(strict_types = 1);

use Civi\Gmailconnect\Helper;
use CRM_Gmailconnect_ExtensionUtil as E;

/**
 * Shows the logged-in contact their personal Gmail Connect URL and lets them
 * regenerate it
 */
class CRM_Gmailconnect_Form_Settings extends CRM_Core_Form {

  public function buildQuickForm(): void {
    $this->setTitle(E::ts('Gmail Connect'));
    $this->assign('endpointUrl', Helper::endpointUrl(Helper::getToken($this->getLoggedInContactId())));
    $this->addButtons([
      [
        'type' => 'submit',
        'name' => E::ts('Regenerate URL'),
        'icon' => 'fa-refresh',
      ],
    ]);
  }

  public function postProcess(): void {
    Helper::regenerateToken($this->getLoggedInContactId());
    CRM_Core_Session::setStatus(
      E::ts('Your previous URL no longer works. Copy the new one into the Gmail add-on.'),
      E::ts('URL regenerated'),
      'success'
    );
    CRM_Core_Session::singleton()->replaceUserContext(CRM_Utils_System::url('civicrm/gmailconnect/settings', 'reset=1'));
  }

  private function getLoggedInContactId(): int {
    $contactId = CRM_Core_Session::getLoggedInContactID();
    if (!$contactId) {
      throw new CRM_Core_Exception(E::ts('You must be logged in to use Gmail Connect.'));
    }
    return $contactId;
  }

}
