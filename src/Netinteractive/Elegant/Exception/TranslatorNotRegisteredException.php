<?php

namespace Netinteractive\Elegant\Exception;

/**
 * Class TranslatorNotRegisteredException
 * @package Netinteractive\Elegant\Exception
 */
class TranslatorNotRegisteredException extends \Exception
{
    /**
     * @param string $name
     * @param string $message
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($name, $message = "", $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        if (empty($message)){
            $message = _("Translator $name is not registered!");
        }
        $this->message = $message;
    }
}
