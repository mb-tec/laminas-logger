<?php
/**
 * @author      Matthias Büsing <info@mb-tec.eu>
 * @copyright   Copyright (c) 2020 Matthias Büsing
 * @link        https://mb-tec.eu
 * @license     MIT
 */

declare(strict_types=1);

namespace MBtec\LaminasLogger\Writer;

use Laminas\Http\Client;
use Laminas\Log\Writer\AbstractWriter;

class SlackWriter extends AbstractWriter
{
    const BOT_NAME = 'Laminas Logger';

    protected Client $client;
    protected ?string $channelOverride = null;

    public function __construct(string $webhookUrl, $channelOverride = null)
    {
        parent::__construct();

        if (is_string($channelOverride)) {
            $this->channelOverride = $channelOverride;
        }

        $this->client = new Client($webhookUrl);
        $this->client
            ->setMethod('POST')
            ->setAdapter(Client\Adapter\Socket::class);
    }

    protected function doWrite(array $event)
    {
        $payload = $this->getFormatter()->format($event);

        if (is_string($payload)) {
            $payload = [
                'text' => $payload,
            ];
        }

        $payload['username'] = self::BOT_NAME;

        if (is_string($this->channelOverride)) {
            $payload['channel'] = $this->channelOverride;
        }

        try {
            $this->client
                ->setRawBody(json_encode($payload))
                ->send();
        } catch (\Exception $ex) {
        }
    }
}