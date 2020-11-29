<?php
/**
 * @author      Matthias Büsing <info@mb-tec.eu>
 * @copyright   2020 Matthias Büsing
 * @license     MIT
 * @link        http://mb-tec.eu
 */

declare(strict_types=1);

namespace MBtec\LaminasLogger\Service;

use Laminas\Log\Filter;
use Laminas\Log\Logger;
use Laminas\Log\Writer\Stream;
use MBtec\LaminasLogger\Writer\SlackWriter;

class LoggerService
{
    const LOGFILE_DEFAULT = 'system.log';
    const LOGFILE_EXCEPTION = 'exception.log';

    private array $config;
    private array $loggers = [];

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function emerg($msg, $logfile = null): void
    {
        $this->log(Logger::EMERG, $msg, $logfile);
    }

    public function alert($msg, $logfile = null): void
    {
        $this->log(Logger::ALERT, $msg, $logfile);
    }

    public function crit($msg, $logfile = null): void
    {
        $this->log(Logger::CRIT, $msg, $logfile);
    }

    public function err($msg, $logfile = null): void
    {
        $this->log(Logger::ERR, $msg, $logfile);
    }

    public function warn($msg, $logfile = null): void
    {
        $this->log(Logger::WARN, $msg, $logfile);
    }

    public function notice($msg, $logfile = null): void
    {
        $this->log(Logger::NOTICE, $msg, $logfile);
    }

    public function info($msg, $logfile = null): void
    {
        $this->log(Logger::INFO, $msg, $logfile);
    }

    public function debug($msg, $logfile = null): void
    {
        $this->log(Logger::DEBUG, $msg, $logfile);
    }

    public function log(int $prio, $msg, $logfile = null): void
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
            $this->getLogger($logfile)->log($prio, $msg);
        }
        catch (\Exception $ex) {
        }
    }

    private function getLogger(string $logfile): Logger
    {
        if (!isset($this->loggers[$logfile])) {
            $logger = new Logger();

            if (isset($this->config['stream']['enabled']) && $this->config['stream']['enabled']) {
                $path = rtrim($this->config['stream']['enabled'], '/');
                $streamWriter = new Stream($path . DIRECTORY_SEPARATOR . $logfile);

                if (isset($this->config['stream']['formatter']) && $this->config['stream']['formatter']) {
                    $streamWriter->setFormatter($this->config['stream']['formatter']);
                }

                if (isset($this->config['stream']['filter']) && $this->config['stream']['filter']) {
                    $streamWriter->addFilter(new Filter\Priority($this->config['stream']['filter']));
                }

                $logger->addWriter($streamWriter);
            }

            if (isset($this->config['slack']['enabled']) && $this->config['slack']['enabled']) {
                if (isset($this->config['slack']['webhook_url']) && $this->config['slack']['webhook_url']) {
                    $slackWriter = new SlackWriter($this->config['slack']['webhook_url']);

                    if (isset($this->config['slack']['formatter']) && $this->config['slack']['formatter']) {
                        $slackWriter->setFormatter($this->config['slack']['formatter']);
                    }

                    if (isset($this->config['slack']['filter']) && $this->config['slack']['filter']) {
                        $slackWriter->addFilter(new Filter\Priority($this->config['slack']['filter']));
                    }

                    $logger->addWriter($slackWriter);
                }
            }

            $this->loggers[$logfile] = $logger;
        }

        return $this->loggers[$logfile];
    }
}