<?php namespace Netinteractive\Elegant\Exception;

/**
 * Class RelationDosntExistsException
 * @package Netinteractive\Elegant\Exception
 */
class RecordNotFoundException extends \Exception
{
    /**
     * @param string $message
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        if (empty($message)){
            $message = _("Record not found!");
        }
        $this->message = $message;
    }
}