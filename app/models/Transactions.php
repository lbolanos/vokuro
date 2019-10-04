<?php
namespace Vokuro\Models;

use Phalcon\Mvc\Model;

/**
 * Transactions
 * Stores transactions
 */
class Transactions extends Model
{
	const TYPE_RETIRO = "R";
	const TYPE_SALDO = "S";
	const TYPE_DEPOSITO = "D";
	const TYPE_TRANSFER = "T";

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $usersId;


	/**
	 *
	 * @var string
	 */
	public $description;

	public $result;

	public $success;

    /**
     *
     * @var string
     */
    public $type;

    /**
     *
     * @var string
     */
    public $createdAt;

    /** @var float */
    public $amount;

    /**
     * Before create the user assign a password
     */
    public function beforeValidationOnCreate()
    {
        // Timestamp the confirmaton
        $this->createdAt = date('Y-m-d H:i:s');
    }

    public function initialize()
    {
        $this->belongsTo('usersId', __NAMESPACE__ . '\Users', 'id', [
            'alias' => 'user'
        ]);
    }


	/**
	 * @param $amount
	 * @param $result
	 * @param $description
	 * @param $success
	 * @param $userId
	 * @param string $type
	 */
	public static function createTx($amount, $result, $description, $success, $userId, $type = Transactions::TYPE_DEPOSITO ) {
		$tx = new Transactions();
		$tx->amount = $amount;
		$tx->result = $result;
		$tx->type = $type;
		$tx->description = $description;
		$tx->createdAt = date('Y-m-d H:i:s');;
		$tx->success = $success;
		$tx->usersId = $userId;
		$tx->save();
	}
}
