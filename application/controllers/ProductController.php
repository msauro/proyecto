<?php

class ProductController extends Gabinando_Base {

	const UPLOADPATHAVATAR = '/resources/admin_avatars/';

	public function init(){
		parent::init();
	}

    public function addAction(){
		if($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();
			
			$file = $_FILES['avatar_url'];
			
			$img = $this->uploadImage(self::UPLOADPATHAVATAR,$file,$params['email']);

			if($img['status'] == 'error'){
				die($img['message']);
			}

			$params['avatar_url'] = $img['message'];
		
			$params["password"]	= base64_encode(pack("H*",sha1(utf8_encode($params["password"]))));

			$product = new Application_Model_Product();

			// Search email before add an product
			$alreadyRegistered = $product->getProductByEmail($params['email']);
			
			if($alreadyRegistered instanceof Exception){
                Gabinando_Base::addError($alreadyRegistered->getMessage());
                $this->_redirect('/products/add');
            // If this email wasn't registered in the past -> add a new product
            }elseif($alreadyRegistered == null){
            	$result = $product->add($params);

				if($result instanceof Exception){
	                Gabinando_Base::addError($result->getMessage());
	                $this->_redirect('/products/add');
            	}
            	Gabinando_Base::addSuccess('Producto agregado correctamente');
            	$this->_redirect('/products/list');
            }else{
            	// If this email was registered in the past and is deleted now
            	// Set idDeleted property to false in order to "reactivate" the registered admin
            	$params['isDeleted'] = 0;
            	// Update the admin with new properties and the same id
            	$result = $product->edit('id_product', $alreadyRegistered['id_product'], $params);

				if($result instanceof Exception){
	                Gabinando_Base::addError($result->getMessage());
	                $this->_redirect('/products/add');
	            }
	            Gabinando_Base::addSuccess('Producto agregado correctamente');
	            $this->_redirect('/products/list');
            }
		}
	}

	public function listAction() {
		$product = new Application_Model_Product();
		$productList = $product->getList();
		$this->view->productList = $productList;
    }
}

?>