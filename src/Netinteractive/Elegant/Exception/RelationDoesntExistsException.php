<?php

namespace Netinteractive\Elegant\Exception;


/**
 * Class RelationDoesntExistsException
 * @package Netinteractive\Elegant\Exception
 */
class RelationDoesntExistsException extends \Exception
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
            $message = _("Relation \"$name\" dosn't exists!");
        }
        $this->message = $message;
    }
}
