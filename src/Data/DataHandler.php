<?php
/**
 * 
 */
namespace Salsa\Data;

/**
 * summary
 */

class DataHandler
{
  private $handler;
  private $returnData;
  private $salsa;

  use Traits\Clean;

  /**
   * summary
   */
  public function __construct( \Salsa\Salsa $salsa )
  {
    $this->salsa = $salsa;
  }

  /**
   * [setHandler description]
   * @param [type] $handler [description]
   */
  public function setHandler($handler)
  {
    $this->handler = $handler;
  }

  /**
   * [getHandler description]
   * @return [type] [description]
   */
  public function getHandler()
  {
    return $this->handler;
  }

  /**
   * [setReturnData description]
   * @param [type] $data [description]
   */
  public function setReturnData($data)
  {
    error_log( print_r( $data,1 ) );
    $this->returnData = $data;
  }

  /**
   * [getReturnData description]
   * @return [type] [description]
   */
  public function getReturnData()
  {
    return $this->returnData;
  }

  /**
   * [getType description]
   * @return [type] [description]
   */
  public function getType()
  {
    return gettype($this->handler);
  }

  /**
   * [process description]
   * @return [type] [description]
   */
  public function process()
  {
    if ( !isset( $this->handler ) ){
      return false;
    }

    $type = $this->getType();
    error_log( print_r( $type,1 ) );
    if ( $type == "object" && is_callable( $this->handler) ) {
      $this->object();
    }
    else if ($type == "string") {
      $this->stringHandler();
    }
    else if ($type == "array") {
      $this->arrayHandler();
    }
  }

  public function objectHandler()
  {
    if ( !isset( $this->handler ) ) {
      return false;
    }
    // TODO intercept and expose certain data etc.
    $this->callFunction();
  }

  public function callFunction()
  {
    $this->setReturnData( call_user_func_array($this->handler, $this->salsa->regex->params));
  }

  public function stringHandler()
  {
    if ( !isset( $this->handler) ) {
      return false;
    }
    $this->outputString($this->handler);
  }

  public function outputString($string)
  {
    echo $string;
  }

  public function arrayHandler()
  {
    if( !isset( $this->handler ) ) {
      return false;
    }
    if (isset($this->handler) && isset($this->handler)) {
      $this->callController($this->handler);
    }
  }

  public function callController($data)
  {
    if( class_exists( $data["controller"] ) ) {
      $controller = new $data["controller"];
    }

    if( $controller && method_exists( $controller, $data["method"] ) ){
      $this->setReturnData( $controller->{$data["method"]}(array_merge($this->salsa->params, is_array($data["passin"]) ? $data["passin"] : array())));
    }
  }
}