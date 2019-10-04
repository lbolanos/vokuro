<?php


namespace Vokuro\Models;

use Phalcon\Mvc\Model\Query;

class BasePHQLHelper {


	/**
	 * Ejecuta un update nativo sobre la bd
	 * @param $dbConnection
	 * @param string $function Funcion desde la que se ejecuta
	 * @param string $select Update Nativo a ejecutar
	 * @param array $bindParams Parámetros a reemplazar en el query
	 * @return boolean
	 */
	public static function executeNativeUpdate($dbConnection, $function, $select, $bindParams = null ) {
		if( !$dbConnection ) {
			global $di;
			$dbConnection = $di->getShared('db');
		}
		$ret = $dbConnection->execute($select, $bindParams);
		return $dbConnection->affectedRows();
	}

	public static function executeQueryNativeSelect($dbConnection, $function = __FUNCTION__, $select = "",
													$fetch = \PDO::FETCH_OBJ, $fetch_argument = null,
													$bindParams = null) {
		if(!$function) {
			$function = __FUNCTION__;
		}
		if( !$dbConnection ) {
			global $di;
			$dbConnection = $di->getShared('db');
		}
		$query = $dbConnection->query( $select, $bindParams );
		$query->setFetchMode( $fetch, $fetch_argument );
		$ret = $query->fetchAll();
		return $ret;
	}

}