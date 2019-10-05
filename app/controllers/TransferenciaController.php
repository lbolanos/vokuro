<?php
namespace Vokuro\Controllers;

use http\Client\Curl\User;
use Vokuro\Forms\TranferenciaForm;
use Vokuro\Models\BasePHQLHelper;
use Vokuro\Models\Transactions;
use Vokuro\Models\Users;

/**
 * View and define permissions for the various profile levels.
 */
class TransferenciaController extends ControllerBase
{

    /**
     * View the permissions for a profile level, and change them if we have a POST.
     */
    public function indexAction()
    {
        $this->view->setTemplateBefore('private');
		$form = new TranferenciaForm();
        if ($this->request->isPost()) {
			if ($this->security->checkToken()) {
				$this->flash->error("Security Token Error");
				$this->view->txResult = "Security Token Error";
			} else {
				$this->view->txResult = self::transferir($this->request->getPost('amount'), $this->auth->getUser(), $this->request->getPost('userId'));
			}
        }
		$this->view->form = $form;
    }

    public static function transferir($amount, $userFrom, $userToId) {
		$description = "";
		$success = "N";
		$userId = 0;
		if ($amount <= 0) {
			$txResult = "Cantidad debe ser mayor a cero";
		} else {
			/** @var Users $userTo */
			$userTo = Users::findFirstById($userToId);
			$description = $userToId;
			if ($userTo) {
				if( $userTo->id != $userFrom->id) {
					$sql = <<<SQL
							UPDATE users 
							SET accountBalance = accountBalance - :amount
							WHERE id = :id 
								AND ( accountBalance - :amount ) > 0
SQL;
					$count = BasePHQLHelper::executeNativeUpdate(null, __FUNCTION__, $sql, [':id' => $userFrom->id, ':amount' => $amount]);
					if ($count > 0) {
						$userId = $userTo->id;
						$description = $userFrom->name;
						$sql = <<<SQL
							UPDATE users 
							SET accountBalance = accountBalance + :amount
							WHERE id = :id 
SQL;
						$count = BasePHQLHelper::executeNativeUpdate(null, __FUNCTION__, $sql, [':id' => $userTo->id, ':amount' => $amount]);
						$txResult = "Transferencia Exitosa";
						$success = "Y";
						Transactions::createTx($amount, $txResult, $description, $success, $userId);
					} else {
						$txResult = "No cuenta con suficientes fondos";
					}
					$userId = $userFrom->id;
					$description = $userTo->name;
				} else {
					$txResult = "No se puede transferir a su misma cuenta";
				}
			} else {
				$txResult = "Cuenta No encontrada";
			}
		}
		Transactions::createTx($amount, $txResult, $description, $success, $userId, Transactions::TYPE_TRANSFER);
		return $txResult;
	}

}
