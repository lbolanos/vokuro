<?php
namespace Vokuro\Controllers;

use Vokuro\Models\BasePHQLHelper;
use Vokuro\Models\Profiles;

/**
 * View and define permissions for the various profile levels.
 */
class BalanceController extends ControllerBase
{

    /**
     * View the permissions for a profile level, and change them if we have a POST.
     */
    public function indexAction()
    {
        $this->view->setTemplateBefore('private');
        // Pass all the active profiles
		$user = $this->auth->getUser();
		$sql = 'SELECT accountBalance FROM users WHERE id = :identification';
		$accountBalance = BasePHQLHelper::executeQueryNativeSelect(null, __FUNCTION__,$sql, \PDO::FETCH_ASSOC, null,[':identification' => $user->id]);
        $this->view->accountBalance = $accountBalance[0]['accountBalance'];
    }
}
