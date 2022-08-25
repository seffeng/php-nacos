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
| Configs   | pushConfig     | 发布配置     |
| Configs   | deleteConfig   | 删除配置     |
| Configs   | getHistoryAccurate | 查询历史版本     |
| Configs   | getHistoryDetail | 查询历史版本详情     |
| Configs   | getHistoryPrevious | 查询配置上一版本信息     |
| Instances | register       | 注册实例     |
| Instances | detail         | 实例详情     |
| Instances | beat           | 发送实例心跳 |
| Instances | unregister     | 注销实例 |

## 项目依赖

| 依赖              | 仓库地址                            | 备注 |
| :---------------- | :---------------------------------- | :--- |
| guzzlehttp/guzzle | https://github.com/guzzle/guzzle | 无   |
| monolog/monolog | https://github.com/Seldaek/monolog | 无   |

### 备注

1、更多示例请参考 tests 目录下测试文件；

2、[更多文档：nacos.io](https://nacos.io)。
