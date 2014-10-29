<?php

use \dmyers\mvc\controller;

class mainController extends controller {

	public function indexAction() {
		$this->data['welcome'] = 'Ready To Go!';
		
		$this->c->view->load('index',$this->data);
	}

} /* end Controller */