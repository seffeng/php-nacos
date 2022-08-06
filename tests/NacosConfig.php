<?php
/**
 * @link http://github.com/seffeng/
 * @copyright Copyright (c) 2022 seffeng
 */
namespace Seffeng\Nacos\Tests;

use PHPUnit\Framework\TestCase;
use Seffeng\Nacos\Nacos;
use Seffeng\Nacos\Handlers\Configs;

class NacosConfig extends TestCase
{
    /**
     * 获取配置
     */
    public function testGet()
    {
        try {
            $conf = $this->getConfig()->getConfig();
            var_dump($conf);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 监听配置
     */
    public function testListener()
    {
        try {
            $conf = $this->getConfig()->listenerConfig();
            var_dump($conf);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function getConfig()
    {
        $host = 'http://nacos.io';
        $nacos = new Nacos($host, 'nacos', 'nacos');
        $config = new Configs($nacos, 'test-01');
        return $config;
    }
}
