<?php
namespace app\client;


class AxerveError extends \Exception
{


    public function __construct(array $error)
    {
        parent::__construct($error['description']);
        $this->code = $error['code'];
    }


}