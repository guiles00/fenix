<?
class Domain_Sesion {
    
    protected $_session_id;
    protected $_usuario;
    protected $_acl;
    private static $_instance;
    
 //Singleton  
    private function __construct(){
        
        $this->_session_id = $_SESSION['userInfo']['usuario_id'];
        
        $this->_usuario = new Domain_Usuario($this->_session_id);
        $this->_acl = new Domain_Acl($this->_session_id) ;
        $this->_usuario->setAcl(new  Domain_Acl($this->_session_id));
    }
   
   public static function getInstance(){
        
        if( self::$_instance instanceof self ) return self::$_instance;
        
        self::$_instance = new self;
        
        return self::$_instance;
    }
    
    //Getters
    function getUsuario(){
        return $this->_usuario;
    }
 
    
}
