<?php
/**
 * @link http://github.com/seffeng/
 * @copyright Copyright (c) 2022 seffeng
 */
namespace Seffeng\Nacos\Handlers;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Seffeng\Nacos\Exceptions\NacosException;

/**
 *
 * @author zxf
 * @date   2022年8月4日
 * @method static Log debug($message, array $context = [])
 * @method static Log error($message, array $context = [])
 * @method static Log info($message, array $context = [])
 * @method static Log notice($message, array $context = [])
 * @method static Log warning($message, array $context = [])
 * @method static Log critical($message, array $context = [])
 * @method static Log alert($message, array $context = [])
 */
class Log
{
    /**
     * 项目名
     * @var string
     */
    private $name = 'nacos-client';

    /**
     *
     * @var Logger
     */
    private $logger;

    /**
     * 日志文件路径
     * @var string
     */
    private $path = 'php://stdout';

    /**
     * 日志等级
     * @var integer
     */
    private $level;

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return Logger
     */
    public function getLogger()
    {
        if (is_null($this->logger)) {
            try {
                $this->logger = new Logger($this->getName());
                $this->logger->pushHandler(new StreamHandler($this->getPath(), $this->getLevel()));
            } catch (\Exception $e) {
                throw new NacosException('日志系统初始失败！' . $e->getMessage());
            }
        }
        return $this->logger;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param string $name
     * @return static
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param string $path
     * @return static
     */
    public function setPath(string $path)
    {
        if (!file_exists($path) && !file_exists(dirname($path))) {
            throw new NacosException('日志路径不存在！');
        }
        $this->path = $path;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param integer $level
     * @return static
     */
    public function setLevel(int $level)
    {
        $this->level = $level;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return integer
     */
    public function getLevel()
    {
        return is_null($this->level) ? Logger::DEBUG : $this->level;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param  string $method
     * @param  mixed $parameters
     * @throws NacosException
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        try {
            if (method_exists($this->getLogger(), $method)) {
                return $this->getLogger()->{$method}(...$parameters);
            } else {
                throw new NacosException('方法｛' . $method . '｝不存在！');
            }
        } catch (NacosException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new NacosException('异常错误：确认方法｛' . $method . '｝是否存在！');
        }
    }
}
