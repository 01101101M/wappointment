<?php

namespace Wappointment\Transports;

use WappoSwift_Mime_SimpleMessage;
use GuzzleHttp\ClientInterface;

class Mailgun extends Transport
{
    /**
     * Guzzle client instance.
     *
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * The Mailgun API key.
     *
     * @var string
     */
    protected $key;

    /**
     * The Mailgun domain.
     *
     * @var string
     */
    protected $domain;

    /**
     * The Mailgun API end-point.
     *
     * @var string
     */
    protected $url;

    /**
     * Create a new Mailgun transport instance.
     *
     * @param  \GuzzleHttp\ClientInterface  $client
     * @param  string  $key
     * @param  string  $domain
     * @return void
     */
    public function __construct(ClientInterface $client, $domain, $key, $area)
    {
        $this->key = $key;
        $this->client = $client;
        $this->url = !empty($area) ? 'https://api.eu.mailgun.net/v3/' : 'https://api.mailgun.net/v3/';
        $this->setDomain($domain);
    }



    /**
     * Get the HTTP payload for sending the Mailgun message.
     *
     * @param  \WappoSwift_Mime_SimpleMessage  $message
     * @param  string  $to
     * @return array
     */
    protected function payload(WappoSwift_Mime_SimpleMessage $message, $to)
    {
        return [
            'auth' => [
                'api',
                $this->key,
            ],
            'multipart' => [
                [
                    'name' => 'to',
                    'contents' => $to,
                ],
                [
                    'name' => 'message',
                    'contents' => $message->toString(),
                    'filename' => 'message.mime',
                ],
            ],
        ];
    }


    /**
     * Get the domain being used by the transport.
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set the domain being used by the transport.
     *
     * @param  string  $domain
     * @return string
     */
    public function setDomain($domain)
    {
        $this->url .= $domain . '/messages.mime';

        return $this->domain = $domain;
    }
}
