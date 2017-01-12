<?php

namespace Netinteractive\Elegant\Exception;

/**
 * Class ParamRequiredException
 * @package Netinteractive\Elegant\Exception
 */
class ParamRequiredException extends \Exception
{

    /**
     * ParamRequiredException constructor.
     * @param string $paramName
     * @param string $message
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($paramName, $message = "", $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        if (empty($message)){
            $message = _('Param %s is required!');
            $message =  sprintf($message,$paramName);
        }
        $this->message = $message;
    }
}