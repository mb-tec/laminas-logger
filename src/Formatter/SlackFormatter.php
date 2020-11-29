<?php
/**
 * @author      Matthias Büsing <info@mb-tec.eu>
 * @copyright   Copyright (c) 2020 Matthias Büsing
 * @link        https://mb-tec.eu
 * @license     MIT
 */

declare(strict_types=1);

namespace MBtec\LaminasLogger\Formatter;

use Laminas\Log\Formatter\Base;
use Laminas\Log\Logger;

class SlackFormatter extends Base
{
    protected $priorityColorMap = [
        Logger::EMERG  => 'danger',
        Logger::ALERT  => 'danger',
        Logger::CRIT   => 'danger',
        Logger::ERR    => 'danger',
        Logger::WARN   => 'warning',
        Logger::NOTICE => 'warning',
        Logger::INFO   => '#439FE0',
        Logger::DEBUG  => '#bababa'
    ];

    /**
     * Formats data into a slack message payload compatible structure
     *
     * For more info see:
     *  - https://api.slack.com/docs/message-attachments
     *  - https://api.slack.com/docs/message-formatting
     *
     * @param array $event event data
     * @return array Slack message payload compatible structure
     */
    public function format($event)
    {
        $baseOutput = parent::format($event);

        $messageRaw = $baseOutput['message'];
        $messageRawData = explode('|', $messageRaw);
        $messageRawData = array_map('trim', $messageRawData);
        $message = $messageRawData[0];

        $color = $this->priorityColorMap[$event['priority']] ?? '#bababa';

        $attachment = [
            'fallback' => $message,
            'text' => $message,
            'color' => $color,
            'mrkdwn_in' => ['text'],
            'fields' => $this->getFieldsData($messageRawData),
            'ts' => $event['timestamp']
        ];

        foreach ($baseOutput['extra'] as $key => $value) {
            if ($key == 'channel') {
                continue;
            }

            $attachment['fields'][] = [
                'title' => $key,
                'value' => $value
            ];
        }

        return ['attachments' => [$attachment]];
    }

    private function getFieldsData(array $messageRawData): array
    {
        $fields = [];

        if (count($messageRawData) > 1) {
            for ($i = 1; $i < count($messageRawData); ++$i) {
                $fieldData = explode(':', $messageRawData[$i], 2);

                $fields[] = [
                    'title' => $fieldData[0],
                    'value' => $fieldData[1] ?? 'unknown value',
                    'short' => true,
                ];
            }
        }

        return $fields;
    }
}