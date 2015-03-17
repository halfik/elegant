<?php
/**
 * Created by PhpStorm.
 * User: halfik
 * Date: 16.03.15
 * Time: 15:43
 */

namespace Netinteractive\Elegant\Exception;


/**
 * Class PrimaryKeyException
 * @package Netinteractive\Elegant\Exception
 */
class PrimaryKeyException extends \Exception
{
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->message = _('Invalid Primary Key!');
    }
}
