<?php
namespace app\models\customer;

class Phone {

	public $number;

	public function __construct($number)
    {
        $this->number = $number;
    }

}