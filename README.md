## nacos

### 安装

```shell
# 安装
$ composer require seffeng/nacos
```

### 目录说明

```
├───src
│   │   Nacos.php
│   ├───Exceptions
│   │       NacosException.php
│   └───Handlers
│           Configs.php
│           Instances.php
│           Log.php
├───tests
│       NacosConfig.php
│       NacosInstance.php
```

### 示例

```php
/**
 * Test
 */
class NacosConfig
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
```

### 方法

| 类        | 方法           | 说明         |
| --------- | -------------- | ------------ |
| Configs   | getConfig()    | 获取配置     |
| Configs   | listenerConfig | 监听配置     |
| Instances | register       | 注册实例     |
| Instances | detail         | 实例详情     |
| Instances | beat           | 发送实例心跳 |

## 项目依赖

| 依赖              | 仓库地址                            | 备注 |
| :---------------- | :---------------------------------- | :--- |
| guzzlehttp/guzzle | https://github.com/guzzle/guzzle | 无   |
| monolog/monolog | https://github.com/Seldaek/monolog | 无   |

### 备注

1、仅支持配置获取和监听；

2、仅支持实例注册和发送心跳；

3、更多示例请参考 tests 目录下测试文件；

4、[更多文档：nacos.io](https://nacos.io)。