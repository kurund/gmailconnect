<?php
declare(strict_types = 1);

namespace Civi\Api4\Action\GmailConnect;

use Civi\Api4\Generic\AbstractAction;
use Civi\Api4\Generic\Result;
use Civi\Gmailconnect\Helper;

/**
 * Find the External Email activity logged for a MessageID
 *
 * Returns an empty result if the email has not been logged
 *
 * @package gmailconnect
 */
class GetActivity extends AbstractAction {

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

    $activityId = Helper::findActivityIdByMessageId($messageId);
    if ($activityId) {
      $result[] = [
        'id' => $activityId,
        'url' => Helper::activityUrl($activityId),
      ];
    }
  }

}
