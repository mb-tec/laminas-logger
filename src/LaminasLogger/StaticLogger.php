<?php
/**
 * @author      Matthias Büsing <info@mb-tec.eu>
 * @copyright   2020 Matthias Büsing
 * @license     Commercial
 * @link        http://mb-tec.eu
 */

declare(strict_types=1);

namespace MBtec\LaminasLogger;

use Laminas\Log\Filter;
use Laminas\Log\Logger;
use Laminas\Log\Writer\Stream;

class StaticLogger
{
    const LOGFILE_DEFAULT = 'system.log';
    const LOGFILE_EXCEPTION = 'exception.log';

    private static array $loggers = [];
    private static ?array $config = null;

    public static function emerg($msg, $logfile = null): void
    {
        static::log(Logger::EMERG, $msg, $logfile);
    }

    public static function alert($msg, $logfile = null): void
    {
        static::log(Logger::ALERT, $msg, $logfile);
    }

    public static function crit($msg, $logfile = null): void
    {
        static::log(Logger::CRIT, $msg, $logfile);
    }

    public static function err($msg, $logfile = null): void
    {
        static::log(Logger::ERR, $msg, $logfile);
    }

    public static function warn($msg, $logfile = null): void
    {
        static::log(Logger::WARN, $msg, $logfile);
    }

    public static function notice($msg, $logfile = null): void
    {
        static::log(Logger::NOTICE, $msg, $logfile);
    }

    public static function info($msg, $logfile = null): void
    {
        static::log(Logger::INFO, $msg, $logfile);
    }

    public static function debug($msg, $logfile = null): void
    {
        static::log(Logger::DEBUG, $msg, $logfile);
    }

    public static function log(int $prio, $msg, $logfile = null): void
    {
        if (is_null($logfile)) {
            $logfile = self::LOGFILE_DEFAULT;
        }

        try {
            if ($msg instanceof \Exception) {
                $msg = $msg->getMessage() . PHP_EOL . $msg->getTraceAsString();
                $logfile = self::LOGFILE_EXCEPTION;
            } elseif (is_object($msg)) {
                $msg = (string) $msg;
            } elseif (is_array($msg)) {
                $msg = print_r($msg, true);
            }
        } catch (\Exception $ex) {
            $msg = 'Message printing error';
        }

        try {
            static::getLogger($logfile)->log($prio, $msg);
        }
        catch (\Exception $ex) {
        }
    }

    private static function getLogger(string $logfile): Logger
    {
        if (!isset(static::$loggers[$logfile])) {
            $config = static::getConfig();
            $logger = new Logger();

            if (isset($config['stream']['enabled']) && $config['stream']['enabled']) {
                $path = rtrim($config['stream']['enabled'], '/');
                $streamWriter = new Stream($path . DIRECTORY_SEPARATOR . $logfile);

                if ($config['stream']['formatter']) {
                    $streamWriter->setFormatter($config['stream']['formatter']);
                }

                if ($config['stream']['filter']) {
                    $streamWriter->addFilter(new Filter\Priority($config['stream']['filter']));
                }

                $logger->addWriter($streamWriter);
            }

            if (isset($config['slack']['enabled']) && $config['slack']['enabled']) {
                $slackWriter = new Writer\SlackWriter($config['slack']['webhook_url']);

                if ($config['slack']['formatter']) {
                    $slackWriter->setFormatter($config['slack']['formatter']);
                }

                if ($config['slack']['filter']) {
                    $slackWriter->addFilter(new Filter\Priority($config['slack']['filter']));
                }

                $logger->addWriter($slackWriter);
            }

            static::$loggers[$logfile] = $logger;
        }

        return static::$loggers[$logfile];
    }

    private static function getConfig(): array
    {
        if (!is_array(static::$config)) {
            chdir(dirname(dirname(__DIR__)));

            if (file_exists('config/autoload/mbtec.laminas-logger.local.php')) {
                $config = require_once 'config/autoload/mbtec.laminas-logger.local.php';
            } else {
                $config = require_once dirname(__DIR__) . '/config/module.config.php';
            }

            if (isset($config['mbtec']['laminas-logger'])) {
                static::$config = $config['mbtec']['laminas-logger'];
            }
        }

        return (array) static::$config;
    }
}
