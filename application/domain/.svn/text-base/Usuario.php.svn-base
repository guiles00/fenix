<?php
class Domain_Usuario {
	private $_model ;
	private $_acl;
	private $_tipo_usuario;
	private $_usuario_tipo_id;
	private $_m_usuario_perfil;

	public function __construct($id=null){

		$model = new Model_Usuario();
		$m_usuario_perfil = new Model_UsuarioPerfil();
		$this->_model = ($id==null)?new $model: $model->getTable()->find($id) ;
		//$this->_usuario_tipo_id = $this->_model->tipo_usuario_id;
		$this->_tipo_usuario = ($id==null)? null : $this->getEntidad();
		//$this->_usuario_tipo_id = $this->getEntidad();
		$this->_m_usuario_perfil = ($id==null)?new Model_UsuarioPerfil() :
		$m_usuario_perfil->getTable()->findOneByUsuarioId($id); //Tiene un solo perfil

	}
	public function getTipoUsuario(){
		return $this->_tipo_usuario;
	}
	public function getModel(){
		return $this->_model;
	}
	public function setAcl($acl){
		$this->_acl = $acl;
	}
	public function getAcl(){
		return $this->_acl ;
	}
	public function getUserPerfil(){
		return $this->getAcl()->userPerfil;
	}
	public function getUserPerfilTemp(){
		return $this->getAcl()->userPerfilTemp;
	}
	public function getModelUsuarioPerfil(){
		return $this->_m_usuario_perfil;
	}

	public function getById($id){
		$row = $this->_model
		->getTable()
		->createQuery()
		->andWhere('usuario_id = ?',$id)
		->execute()
		->toArray();
		return $row;
	}

	public function getEntidad(){
		//traigo el nombre
		//@TODO
		//1-Se podria poner como constantes
		$id = ($this->_model == null)? 0 : $this->_model->tipo_usuario_id;
		$nombre = Domain_Helper::getHelperNameById('entidad',$id);

		switch ($nombre) {
			case 'compania':
				return new Domain_Compania($this->_model->usuario_tipo_id);
				break;
			case 'productor':
				throw new Exception('Falta terminar');
				break;
			case 'cliente':
				return new Domain_Cliente($this->_model->usuario_tipo_id);
				break;
			case 'operador':
				return new Domain_Operador();
				break;
			case 'agente':
				return new Domain_Agente($this->_model->usuario_tipo_id);
				break;
			case 'ejecutivo_cuenta':
				return new Domain_Agente($this->_model->usuario_tipo_id);
				break;	
			default:
				throw new Exception('No tiene Tipo de usuario asignado');


		}
	}
	public static function getPerfilIdById($id){
		$m_usuario = new Model_UsuarioPerfil();
		
		
		$usuario_perfil_id = $m_usuario->getTable()->findOneByUsuarioId($id)->toArray();
		//echo "Perfil:";
	//	print_r($usuario_perfil_id);
		return $usuario_perfil_id['perfil_id'];
	}
	public static function getUsuarioByName($username){
		$m_usuario = new Model_Usuario();
		
		$usuario = $m_usuario->getTable()->findOneByUsername($username);
		
		return $usuario;
	}
}