<?php
/**
 * @author      Matthias Büsing <info@mb-tec.eu>
 * @copyright   2020 Matthias Büsing
 * @license     MIT
 * @link        http://mb-tec.eu
 */

use Laminas\Log\Logger;
use MBtec\LaminasLogger\Service;
use MBtec\LaminasLogger\Formatter;

return [
    'mbtec' => [
        'laminas-logger' => [
            'stream' => [
                'enabled' => true,
                'dir' => 'data/log/',
                'formatter' => null,
                'filter' => null,
            ],
            'slack' => [
                'enabled' => false,
                'webhook_url' => '',
                'formatter' => Formatter\SlackFormatter::class,
                'filter' => Logger::NOTICE,
            ],
        ],
    ],

    'service_manager' => [
        'factories' => [
            Service\LoggerService::class => Service\LoggerServiceFactory::class,
        ],
    ],
];