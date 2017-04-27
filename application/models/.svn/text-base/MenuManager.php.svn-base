<?php
/**
 *
 * version 1.0
 */

/**
 * Menu Manager
 *
 * Maneja los Menus, Crea interfases JSON y objetos JQGrid Compatibles
 * Tambien tiene un metodo que devuelve un array con el menu completo
 * correspondiente al ACL del usuario logueado.
 *
 * @uses       ...
 * @subpackage Model
 */
class Model_MenuManager
{
	private function _urlToAclResource($url){
		/**
		 * acomodo la url para que me quede modulo*controlador*accion,
		 * (saco slash del principio y del final, luego cambio los slash internos por asteriscos)
		 */
		$url=(substr($url, 0,1)!='/')?$url:substr($url,1);
		$url=(substr($url,-1,1)!='/')?$url:substr($url,0,strlen($url)-1);
		$url=str_replace('/','*',$url);
		return $url;
	}

	/**
	 * Pensado para consultarse directamente desde la Vista y Armar el Menu del Usuario
	 * Se Arma el menu segun la Base de Datos y el ACL del usuario.
	 * */
	public function getMenuByPerfilId(array $array)
	{
		//Armo un array con los datos del menu
		//Ordena hasta dos niveles de profunidad (0 y 1)
		$model=new Model_Menu();
		$perfilId=$array['perfilId'];

		//Traigo a todos los padres
		$data_menues=$model
		->getTable()
		->createQuery()
		->andWhere('padre_id = ?',0)
		->orderBy('orden')
		->execute()
		->toArray();
		$menu=array();

		$logger = Zend_Registry::get('logger');
//$logger->log($data_menues, Zend_Log::INFO);
	//echo"<pre>";
   // print_r($data_menues);
	$perfil_menu = array();

		foreach ($data_menues as $menues) {
			
			if($this->estaEnPerfil( array($menues['menu_id'],$perfilId) ))
			{
			//echo"esta";
			//print_r($menues);
			$perfil_menu[] = $menues; 
			}
		}
//print_r($perfil_menu);
		//exit;
			
		$menu=array();
		$menu_padre = array();
		//echo"<pre>";
		foreach($perfil_menu as $data_menu){

			$data_menu_items=$model
			->getTable()
			->createQuery()
			->andWhere('padre_id = ?',$data_menu['menu_id'])
			->orderBy('orden')
			->execute()
			->toArray();


			//$logger->log("Items dentro:".$data_menu['menu'], Zend_Log::INFO);

			$menu[$data_menu['menu']] = array();

//echo"<pre>";
			foreach($data_menu_items as $items){

				//$logger->log($temp, Zend_Log::INFO);
				//$logger->log($items, Zend_Log::INFO);
				//Si el perfil lo permite lo agrega
				//	print_r($items);
				if($this->estaEnPerfil( array($items['menu_id'],$perfilId)) ){
//echo "esta<br>";
//print_r($items);
				$menu[$data_menu['menu']][] =array( 'nombre'=> $items['menu'] , 'url'=> $items['url']);

				}
			}

		}

		return $menu;
	}


	public function getMenuAndItems()
	{
		//Armo un array con los datos del menu
		//Ordena hasta dos niveles de profunidad (0 y 1)
		$model=new Model_Menu();


		//Traigo a todos los padres
		$data_menues=$model
		->getTable()
		->createQuery()
		->andWhere('padre_id = ?',0)
		->orderBy('orden')
		->execute()
		->toArray();
		$menu=array();

		//$logger = Zend_Registry::get('logger');
			

		//$logger->log($data_menues, Zend_Log::INFO);
			
		$menu=array();
		$menu_padre = array();
		//echo"<pre>";
		foreach($data_menues as $data_menu){

			$data_menu_items=$model
			->getTable()
			->createQuery()
			->andWhere('padre_id = ?',$data_menu['menu_id'])
			->orderBy('orden')
			->execute()
			->toArray();


			//$logger->log("Items dentro:".$data_menu['menu'], Zend_Log::INFO);

			$menu[$data_menu['menu']] = array();


			foreach($data_menu_items as $items){

				//$logger->log($temp, Zend_Log::INFO);
				//$logger->log($items, Zend_Log::INFO);
				//Si el perfil lo permite lo agrega
				if($this->estaEnPerfil($items['menu_id'])){

				$menu[$data_menu['menu']][] =array( 'nombre'=> $items['menu'] , 'url'=> $items['url']);
				
				}
			}

		}

		
		return $menu;
	}

	public function estaEnPerfil(array $array){
		//echo"<pre>";
		$menu_perfil=new Model_MenuPerfil();
		$data =$menu_perfil
		->getTable()
		->createQuery()
		->andWhere('menu_id = ? and perfil_id= ?',$array)
		//->getSqlQuery();
		->execute()
		->toArray();
	

		//print_r($data);
		//print_r($array);
		if(empty($data)) return false;
//exit;
		return true;
	}

}
