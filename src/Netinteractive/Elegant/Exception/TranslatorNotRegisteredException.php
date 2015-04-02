<?php namespace Netinteractive\Elegant\Exception;

/**
 * Class TranslatorNotRegisteredException
 * @package Netinteractive\Elegant\Exception
 */
class TranslatorNotRegisteredException extends \Exception
{
    public function __construct($name, $message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        if (empty($message)){
            $message = _("Translator $name is not registered!");
        }
        $this->message = $message;
    }
}