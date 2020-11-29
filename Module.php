<?php
/**
 * @author      Matthias Büsing <info@mb-tec.eu>
 * @copyright   2020 Matthias Büsing
 * @license     MIT
 * @link        http://mb-tec.eu
 */

declare(strict_types=1);

namespace MBtec\LaminasLogger;

use Laminas\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{
    public function getConfig(): array
    {
        return include __DIR__ . '/config/module.config.php';
    }
}
