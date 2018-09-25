<?php
namespace Salsa;

// use Salsa\DataCollection\DataCollection;

class ServiceProvider {

	protected $request;
	protected $response;
	protected $session_id;
	protected $layout;
	protected $view;
	protected $shared_data;

	public function __construct( Request $request = null, AbstractResponse $response = null ){
		$this->bind( $request, $response );

		// $this->shared_data = new DataCollection();
	}

	public function bind( Request $request = null, AbstractResponse $response = null ){
		$this->request = $request ?: $this->request;
		$this->response = $response ?: $this->response;
		return $this;
	}

}