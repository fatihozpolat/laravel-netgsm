<?php

namespace Fatihozpolat\Netgsm\Channels;

use Exception;
use Fatihozpolat\Netgsm\Messages\NetgsmMessage;
use Fatihozpolat\Netgsm\Netgsm;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Notifications\Notification;

class NetgsmChannel
{
    /**
     * NetgsmChannel constructor.
     *
     * @throws GuzzleException
     * @throws Exception
     */
    public function send(object $notifiable, Notification $notification): ?array
    {
        if (! ($to = $notifiable->routeNotificationFor('netgsm'))) {
            return null;
        }

        if (! method_exists($notification, 'toNetgsm')) {
            throw new Exception('toNetgsm method not found.');
        }

        if (! ($message = $notification->toNetgsm($notifiable)) instanceof NetgsmMessage) {
            throw new Exception('toNetgsm method should return NetgsmMessage instance.');
        }

        return (new Netgsm)->sendSms([$to], $message->message);
    }
}
