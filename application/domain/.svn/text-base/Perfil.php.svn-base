<?php
class Domain_Perfil {
	private $_model ;
	private $_model_menu_perfil ;

	public function __construct($id=null){

		$model = new Model_Perfil();
		$this->_model = ($id==null)?new $model: $model->getTable()->find($id) ;
		$this->_model_menu_perfil = new Model_MenuPerfil();

	}
	public function getModel(){
		return $this->_model;
	}

	public function getById($id){
		$row = $this->_model
		->getTable()
		->createQuery()
		->andWhere('perfil_id = ?',$id)
		->execute()
		->toArray();
		return $row;
	}
	public function deleteByPerfilId($id){

		$this->_model_menu_perfil->getTable()->findByPerfilId($id)->delete();
		//echo "<pre>";
		//print_r($temp);
		//exit;
	}
	public function addMenuByPerfil($perfil_id,$menu_items){

		foreach ($menu_items as $menu_id){
			if(!empty($menu_id)){
				$m_menu_perfil = new Model_MenuPerfil();
				$m_menu_perfil->perfil_id = $perfil_id;
				$m_menu_perfil->menu_id = $menu_id;
				$m_menu_perfil->save();
			}
		}

	}
	public static function existsMenu($perfil_id,$menu_id){
		$m_menu_perfil = new Model_MenuPerfil();
		
		$menu_id =  $m_menu_perfil->getTable()
		->createQuery()
		->andWhere('perfil_id = ? and menu_id= ?',array($perfil_id,$menu_id))
		->execute()
		->toArray();
		
		$empty = empty($menu_id);
		return $empty;
	}

	public static function getPerfiles(){
		$m_perfil = new Model_Perfil();
		return $m_perfil->getTable()->findAll()->toArray();
	}
}
