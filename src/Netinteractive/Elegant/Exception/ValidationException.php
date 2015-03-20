<?php namespace Netinteractive\Elegant\Exception;


use Illuminate\Support\MessageBag AS MessageBag;

/**
 * Class ValidationException
 * @package Netinteractive\Elegant\Exception
 */
class ValidationException  extends \Exception
{
    /**
     * @var Illuminate\Support\MessageBag
     */
    protected $messageBag;

    /**
     * @param MessageBag $messageBag
     * @param string $message
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct(MessageBag $messageBag = null, $message = "", $code = 0, \Exception $previous = null){
        $this->messageBag = $messageBag;
        return parent::__construct($message, $code,$previous);
    }

    /**
     * @return MessageBag
     */
    public function getMessageBag(){
        return $this->messageBag;
    }
}