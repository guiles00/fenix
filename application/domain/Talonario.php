<?php
class Domain_Talonario {
	
	private static $instance;

	private function __construct()
	{
	}

	public static function getInstance()
	{
		if (!isset(self::$instance)) {

			echo 'Crea nueva instancia.';
			$className = __CLASS__;

			self::$instance = new $className;
		}
		return self::$instance;
	}


	public function __clone()
	{
		trigger_error('Clone is not allowed.', E_USER_ERROR);
	}

	public function __wakeup()
	{
		trigger_error('Unserializing is not allowed.', E_USER_ERROR);
	}
	

	public function getNumeroByCodigo($codigo){
		$query = Doctrine_Query::create()
		->from('Model_Talonario')
		->Where('codigo = ?',$codigo)
		->execute()
		->toArray();

		return $query[0]['numero'];
	}

	public function incrementaNumeroByCodigo($codigo){

		$query = Doctrine_Query::create()
		->update('Model_Talonario')
		->set('numero', 'numero + 1')
		->where('codigo =?',$codigo)
		->execute();

		return $query;

	}
}