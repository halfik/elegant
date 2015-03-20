<?php namespace Netinteractive\Elegant\Exception;


/**
 * Class PrimaryKeyIncrementException
 * @package Netinteractive\Elegant\Exception
 */
class PrimaryKeyIncrementException extends \Exception
{
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->message = _('Multipile PK cant be autoincremented!');
    }
}