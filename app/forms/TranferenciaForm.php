<?php


namespace Vokuro\Forms;


use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Numeric;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;
use Phalcon\Validation\Validator\Identical;
use Phalcon\Validation\Validator\Numericality;
use Phalcon\Validation\Validator\PresenceOf;

class TranferenciaForm  extends Form {

	public function initialize()
	{
		$userId = new Text('userId', [
			'placeholder' => 'Cuenta Destino'
		]);

		$userId->addValidators([
			new PresenceOf([
				'message' => 'Cuenta Destino es requerida'
			])
		]);
		$this->add($userId);

		$amount = new Numeric('amount', [
			'placeholder' => 'Cantidad a Transferir'
		]);
		$amount->addValidators([
			new PresenceOf([
				'message' => 'La Cantidad de Dinero a transferir es requerida'
			]),
			new Numericality([
  			'message' => ':field is not numeric'
			])
		]);
		$this->add($amount);

		// CSRF
		$csrf = new Hidden('csrf');

		$csrf->addValidator(new Identical([
			'value' => $this->security->getSessionToken(),
			'message' => 'CSRF validation failed'
		]));

		$csrf->clear();

		$this->add($csrf);

		$this->add(new Submit('go', [
			'class' => 'btn btn-success'
		]));
	}
}