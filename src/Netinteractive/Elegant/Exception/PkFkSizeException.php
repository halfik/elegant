<?php namespace Netinteractive\Elegant\Exception;


/**
 * Class PkFkSizeException
 * @package Netinteractive\Elegant\Exception
 */
class PkFkSizeException extends \Exception
{
    /**
     * @param array $pk
     * @param array $fk
     * @param string $message
     * @param int $code
     * @param Exception $previous
     */
    public function __construct(array $pk, array $fk, $message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        if (empty($message)){
            $message = _('Primary key has a different size than the foreign key!').' [PK='.count($pk).']  [FK='.count($fk).']';
        }
        $this->message = $message;
    }
}
