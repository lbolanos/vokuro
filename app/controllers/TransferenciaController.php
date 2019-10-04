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
			$description = "";
			$success = "N";
			$amount = 0;
			$userId = 0;
			if ($this->security->checkToken()) {
				$this->flash->error("Security Token Error");
				$this->view->txResult = "Security Token Error";
			} else {
				$amount = $this->request->getPost('amount');
				if ($amount <= 0) {
					$this->view->txResult = "Cantidad debe ser mayor a cero";
				} else {
					$userFrom = $this->auth->getUser();
					/** @var Users $userTo */
					$userTo = Users::findFirstById($this->request->getPost('userId'));
					$description = $this->request->getPost('userId');
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
								$this->view->txResult = "Transferencia Exitosa";
								$success = "Y";
								Transactions::createTx($amount, $this->view->txResult, $description, $success, $userId);
							} else {
								$this->view->txResult = "No cuenta con suficientes fondos";
							}
							$userId = $userFrom->id;
							$description = $userTo->name;
						} else {
							$this->view->txResult = "No se puede transferir a su misma cuenta";
						}
					} else {
						$this->view->txResult = "Cuenta No encontrada";
					}
				}
			}
			Transactions::createTx($amount, $this->view->txResult, $description, $success, $userId, Transactions::TYPE_TRANSFER);
        }
		$this->view->form = $form;
    }
}
