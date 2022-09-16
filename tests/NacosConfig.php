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

    /**
     * 删除配置
     */
    public function testDelete()
    {
        try {
            $conf = $this->getConfig()->deleteConfig();
            var_dump($conf);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 发布配置
     */
    public function testPush()
    {
        try {
            $conf = $this->getConfig()->setContent(json_encode(['a' => 'aa01', 'b' => 'bb02', 'c' => '中文测试'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))->setType('json')->pushConfig();
            var_dump($conf);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 历史版本
     */
    public function testHistoryAccurate()
    {
        try {
            $conf = $this->getConfig()->getHistoryAccurate();
            print_r(json_decode($conf));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 历史版本详情
     */
    public function testHistoryDetail()
    {
        try {
            $conf = $this->getConfig()->setId(1)->getHistoryDetail();
            print_r(json_decode($conf));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 查询配置上一版本信息
     */
    public function testHistoryPrevious()
    {
        try {
            $conf = $this->getConfig()->setId(1)->getHistoryPrevious();
            print_r(json_decode($conf));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function getConfig()
    {
        $host = 'http://nacos-io';
        $nacos = new Nacos($host, 'nacos', 'nacos');
        $config = new Configs($nacos, 'test-01');
        return $config;
    }
}
