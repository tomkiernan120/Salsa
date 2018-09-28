<?php
namespace Salsa\DataCollection;

class DataCollection {

	protected $attributes = array();

	public function __construct(array $attributes = array())
    {
        $this->attributes = $attributes;
    }

    public function set($key, $value)
    {
        $this->attributes[$key] = $value;
        return $this;
    }

}