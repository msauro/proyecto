<?php
require_once 'Zend/Loader/Autoloader.php';

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap{

    protected function _initRoutes() {
        $router = Zend_Controller_Front::getInstance()->getRouter();

        $route = new Zend_Controller_Router_Route('api/requests/create', array('controller' => 'Api_Requests', 'action' => 'create'));
        $router->addRoute('create', $route);

        $route = new Zend_Controller_Router_Route('api/requests/cancel', array('controller' => 'Api_Requests', 'action' => 'cancel'));
        $router->addRoute('cancel', $route);

        $route = new Zend_Controller_Router_Route('api/requests/update', array('controller' => 'Api_Requests', 'action' => 'update'));
        $router->addRoute('update', $route);

        $route = new Zend_Controller_Router_Route('api/requests/types', array('controller' => 'Api_Requests', 'action' => 'types'));
        $router->addRoute('types', $route);

        $route = new Zend_Controller_Router_Route('api/requests', array('controller' => 'Api_Requests', 'action' => 'list'));
        $router->addRoute('list', $route);

        $route = new Zend_Controller_Router_Route('api/requests/user/:id_user', array('controller' => 'Api_Requests', 'action' => 'listbyuser'));
        $router->addRoute('listByUser', $route);

        $route = new Zend_Controller_Router_Route('api/requests/guest/:id_guest', array('controller' => 'Api_Requests', 'action' => 'listbyguest'));
        $router->addRoute('listByGuest', $route);


        ///
        /// CANCELL OLD REQUETS 
        ///
        $route = new Zend_Controller_Router_Route('api/requests/canceloldrequests', array('controller' => 'Api_Requests', 'action' => 'canceloldrequests'));
        $router->addRoute('canceloldrequests', $route);
 
	}

    protected function _initDefineConstants(){
        $constantFile = APPLICATION_PATH . '/configs/custom.ini';
        $iniParser = new Zend_Config_Ini($constantFile);
        
        foreach ($iniParser->toArray() as $constName => $constantVal) {
            define($constName, $constantVal);
        }
    }
}