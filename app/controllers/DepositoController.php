<?php
namespace Vokuro\Controllers;

use Vokuro\Forms\TranferenciaForm;
use Vokuro\Models\BasePHQLHelper;
use Vokuro\Models\Transactions;


/**
 * View and define permissions for the various profile levels.
 */
class DepositoController extends ControllerBase
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
					if ($userFrom) {
						$userId = $userFrom->id;
						$sql = <<<SQL
							UPDATE users 
							SET accountBalance = accountBalance + :amount
							WHERE id = :id 
SQL;
						$count = BasePHQLHelper::executeNativeUpdate(null, __FUNCTION__, $sql, [':id' => $userFrom->id, ':amount' => $amount]);
						if ($count > 0) {
							$this->view->txResult = "Depósito Exitoso";
							$success = "Y";
						} else {
							$this->view->txResult = "Error desconocido";
						}

					} else {
						$this->view->txResult = "Cuenta No encontrada";
					}
				}
			}
			Transactions::createTx($amount, $this->view->txResult, $description, $success, $userId);
		}
		$this->view->form = $form;
    }

}
