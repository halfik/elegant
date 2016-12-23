<?php

namespace Netinteractive\Elegant\Exception;

/**
 * Class RecordNotFoundException
 * @package Netinteractive\Elegant\Exception
 */
class RecordNotFoundException extends \Exception
{

    /**
     * @var string|null
     */
    protected $recordName = null;

    /**
     * @param string $message
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($message = "", $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        if (empty($message)){
            $message = _("Record not found!");
        }

        $this->message = $message;
    }

    /**
     * Sets record class name
     * @param string $name
     * @return $this
     */
    public function setRecord($name)
    {
        $this->recordName = $name;

        $this->message =  sprintf( _('Record %s not found!'), $this->recordName);

        return $this;
    }
}