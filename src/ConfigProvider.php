<?php
/**
 * @author      Matthias Büsing <info@mb-tec.eu>
 * @copyright   2020 Matthias Büsing
 * @license     Commercial
 * @link        http://mb-tec.eu
 */

declare(strict_types=1);

namespace MBtec\LaminasLogger;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
        ];
    }

    public function getDependencyConfig(): array
    {
        return [
            'factories' => [
                Service\LoggerService::class => Service\LoggerServiceFactory::class,
            ],
        ];
    }
}
