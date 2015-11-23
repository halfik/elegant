<?php namespace Netinteractive\Elegant\Exception;


/**
 * Class ClassTypeException
 * @package Netinteractive\Elegant\Exception
 */
class ClassTypeException extends \Exception
{
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        if (empty($message)){
            $message = _(' Invliad class type of object');
        }
        $this->message = $message;
    }
}