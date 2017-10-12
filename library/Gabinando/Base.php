<?php
include("ImageResize.php");
abstract class Gabinando_Base extends Zend_Controller_Action {

	public $admin_session;

    public function init(){
        parent::init();

        // Iniciamos la sesion
        $this->admin_session = new Zend_Session_Namespace('gabinando_metre');

        $this->isAdminSession();

        $this->view->admin = $this->admin_session->admin;

        // If there are any message -> set it to the view
        if($this->admin_session->message) $this->view->message = $this->admin_session->message;

        // Setteamos layout de admin
        $layout = $this->_helper->layout();
        $layout->setLayout('layout');
    }

    protected function isAdminSession() {
        $controller = $this->getRequest()->getControllerName();
        $action = $this->getRequest()->getActionName();
        // Si no está seteada la admin_session y el controlador/action no es admin/login, redireccionar al login
        // if(!isset($this->admin_session->admin)){
        //     if("{$controller}/{$action}"!="index/login"){
        //         $this->_redirect('index/login');
        //         }
        // // Si esta seteada la admin_session, el admin logueado NO es superadmin y el controlador es admin,  redireccionar al dashboard
        // } elseif( $this->admin_session->admin['isSuperAdmin'] == 0) {
        //     if("{$controller}"=="admin"){
        //         if("{$action}"!="edit"){
        //             $this->_redirect('/');
        //         }    
        //     }  
        // } 
    }

    public function addError($msg){
        $this->admin_session->message = array('status'=>'error','msg'=>$msg);
    }

    public function addSuccess($msg){
        $this->admin_session->message = array('status'=>'success','msg'=>$msg);
    }


    public function postDispatch() {
        // Clean up saved messages
        if($this->admin_session->message)
            $this->admin_session->message = null;
    }

    public function uploadImage($path,$file,$name,$normal=NULL){
        $name = str_replace(' ', '-', $name);
        $name = preg_replace('/[^A-Za-z0-9\-]/', '', $name);
        $name = strtolower($name);
        $filename = $file['name'];
        $tmp = explode('.', $filename);
        $extension = end($tmp);
        $newfilename = $name . '.' . $extension;
        $image = $_SERVER["DOCUMENT_ROOT"] . $path . $newfilename;
        
        if(!copy($file['tmp_name'], $image)){
            return array('status'=>'error','message'=>'Error uploading image');
        }

        $imgr = new imageResizing();
        $imgr->load($image);

        if (!$normal) {
            // Recortamos la imagen para que sea un cuadrado
            $square = $imgr->getHeight();
            if ($imgr->getWidth() <= $imgr->getHeight()) {
                $square = $imgr->getWidth();
            }

            $xImg = ($imgr->getWidth() / 2) - ($square / 2);
            $yImg = ($imgr->getHeight() / 2) - ($square / 2);
            $imgr->resize($square,$square,$xImg,$yImg);
        }
        
        $imgr->save($image);

        return array('status'=>'success','message'=>$path . $newfilename);
    }
}