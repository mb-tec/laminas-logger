<?php
/**
 * @author      Matthias Büsing <info@mb-tec.eu>
 * @copyright   2020 Matthias Büsing
 * @license     MIT
 * @link        http://mb-tec.eu
 */

declare(strict_types=1);

namespace MBtec\LaminasLogger\Service;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class LoggerServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): LoggerService
    {
        $config = $container->get('config')['mbtec']['laminas-logger'];

        return new LoggerService($config);
    }
}
