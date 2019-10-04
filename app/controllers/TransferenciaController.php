<?php
namespace Vokuro\Controllers;

use Vokuro\Forms\TranferenciaForm;
use Vokuro\Models\BasePHQLHelper;
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
				return;
			}
			$amount = $this->request->getPost('amount');
			if( $amount <= 0 ) {
				$this->view->txResult = "Cantidad debe ser mayor a cero";
			} else {
				$userFrom = $this->auth->getUser();
				$userTo = Users::findFirstById($this->request->getPost('userId'));
				if ($userTo) {
					$sql = <<<SQL
						UPDATE users 
						SET accountBalance = accountBalance - :amount
						WHERE id = :id 
							AND ( accountBalance - :amount ) > 0
SQL;
					$count = BasePHQLHelper::executeNativeUpdate(null, __FUNCTION__, $sql, [':id' => $userFrom->id, ':amount' => $amount]);
					if ($count > 0) {
						$sql = <<<SQL
						UPDATE users 
						SET accountBalance = accountBalance + :amount
						WHERE id = :id 
SQL;
						$count = BasePHQLHelper::executeNativeUpdate(null, __FUNCTION__, $sql, [':id' => $userTo->id, ':amount' => $amount]);
						$this->view->txResult = "Transferencia Exitosa";
					} else {
						$this->view->txResult = "No cuenta con suficientes fondos";
					}

				} else {
					$this->view->txResult = "Cuenta No encontrada";
				}
			}
        }
		$this->view->form = $form;
    }
}
