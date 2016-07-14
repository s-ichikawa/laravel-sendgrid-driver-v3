<?php
namespace Sichikawa\LaravelSendgridDriver;

use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Sichikawa\LaravelSendgridDriver\Transport\SendgridTransport;

class TransportManager extends \Illuminate\Mail\TransportManager
{
    /**
     * Create an instance of the SendGrid Swift Transport driver.
     *
     * @return Transport\SendgridTransport
     */
    protected function createSendgridDriver()
    {
        $config = $this->app['config']->get('services.sendgrid', []);
        $client = new Client(Arr::get($config, 'guzzle', []));
        $pretend = isset($config['pretend']) ? $config['pretend'] : false;
        return new SendgridTransport($client, $config['api_key'], $pretend);
    }
}
