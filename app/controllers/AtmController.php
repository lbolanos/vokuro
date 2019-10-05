<?php


namespace Vokuro\Controllers;


use Phalcon\Mvc\Controller;
use Vokuro\Auth\Exception;
use Vokuro\Models\BasePHQLHelper;
use Vokuro\Models\Transactions;
use Vokuro\Models\Users;

class AtmController  extends ControllerBase {

	static $key = "eEAfR|_&G&f,+vU]";

	/** @var Users  */
	private $user = null;

	public function retirarAction(){
		$globalparams = $this->getGlobalParams();
		$success = "N";
		$txResult = "Error";
		$amount = $globalparams['amount'];
		if ($amount <= 0) {
			$txResult = "Cantidad debe ser mayor a cero";
		} else {
			$userId = $this->user->id;
			$sql = <<<SQL
				UPDATE users 
				SET accountBalance = accountBalance - :amount
				WHERE id = :id 
					AND ( accountBalance - :amount ) > 0
SQL;
			$count = BasePHQLHelper::executeNativeUpdate(null, __FUNCTION__, $sql, [':id' => $userId, ':amount' => $amount]);
			if ($count > 0) {
				$txResult = "Retiro Exitoso";
				$success = "Y";
			} else {
				$txResult = "No cuenta con suficientes fondos";
			}
			Transactions::createTx($amount, $txResult, "", $success, $userId, Transactions::TYPE_RETIRO);
		}
		if( $success == "Y") {
			$this->response->setJsonContent(["accountBalance" => $this->user->accountBalance, "notify" => $txResult, "result" => true]);
		} else {
			$this->response->setJsonContent(["accountBalance" => 0, "notify" => $txResult, "result" => false]);
		}
		return $this->response;
	}

	public function getbalanceAction(){
		$globalparams = $this->getGlobalParams();
		if(isset( $globalparams) ) {
			$this->response->setJsonContent(["accountBalance" => $this->user->accountBalance, "notify" => "Exitoso", "result" => true]);
		} else {
			$this->response->setJsonContent(["accountBalance" => 0, "notify" => "Error", "result" => false]);
		}
		return $this->response;
	}

	public function transferirAction(){
		$globalparams = $this->getGlobalParams();
		if(isset( $globalparams) ) {
			$amount = $globalparams['amount'];
			$acccountId = $globalparams['acccountId'];
			$txResult = TransferenciaController::transferir($amount, $this->user, $acccountId);
			$this->response->setJsonContent(["accountBalance" => $this->user->accountBalance, "notify" => $txResult, "result" => true]);
		} else {
			$this->response->setJsonContent(["accountBalance" => 0, "notify" => "Error", "result" => false]);
		}
		return $this->response;
	}

	public function getGlobalParams( ) {
		if ($this->request->isPost()) {
			$data = $this->request->getRawBody();
			$params = json_decode($data, true);
			$enc = $params['encData'];
			$hash = $params['hash'];
			$hashToCompare = md5($enc);
			if ($hashToCompare == $hash) {
				$output = self::decrypt($enc, self::$key);
				$globalparams = json_decode($output, true);
				if(isset( $globalparams) ) {
					$user = Users::findFirstByEmail($globalparams['username']);
					if ($user && $this->security->checkHash($globalparams['password'], $user->password)) {
						$this->user = $user;
						return $globalparams;
					}
				}
			}
		}
		return null;
	}


	public static function encrypt($text,$key){
		$block = mcrypt_get_block_size('rijndael_128', 'ecb');
		$pad = $block - (strlen($text) % $block);
		$text .= str_repeat(chr($pad), $pad);
		return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $text, MCRYPT_MODE_ECB));
	}

	public static function decrypt($str, $key){
		$str = base64_decode($str);
		$str = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $str, MCRYPT_MODE_ECB);
		$block = mcrypt_get_block_size('rijndael_128', 'ecb');
		$pad = ord($str[($len = strlen($str)) - 1]);
		$len = strlen($str);
		$pad = ord($str[$len-1]);
		return substr($str, 0, strlen($str) - $pad);
	}
}