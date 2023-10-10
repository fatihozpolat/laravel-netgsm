<?php

namespace Fatihozpolat\Netgsm\Messages;

class NetgsmMessage
{
    public string $message;

    public function __construct($message = '')
    {
        $this->message = $message;
    }
}
