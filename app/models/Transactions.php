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
}
