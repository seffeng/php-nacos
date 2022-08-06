<?php
/**
 * @link http://github.com/seffeng/
 * @copyright Copyright (c) 2022 seffeng
 */
namespace Seffeng\Nacos\Tests;

use PHPUnit\Framework\TestCase;
use Seffeng\Nacos\Nacos;
use Seffeng\Nacos\Handlers\Instances;

class NacosInstance extends TestCase
{
    /**
     * 查询实例详情
     */
    public function testDetail()
    {
        try {
            $result = $this->getInstance()->detail();
            print_r($result);exit;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 注册实例
     */
    public function testRegister()
    {
        try {
            $result = $this->getInstance()->register();
            var_dump($result);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 发送实例心跳
     */
    public function testBeat()
    {
        try {
            $result = $this->getInstance()->beat();
            var_dump($result);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function getInstance()
    {
        $host = 'http://nacos.io';
        $nacos = new Nacos($host, 'nacos','nacos');
        $instance = new Instances($nacos, 'local-server');
        $instance->setIp('127.0.0.1')->setPort(80);
        return $instance;
    }
}
