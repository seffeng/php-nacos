<?php
/**
 * @link http://github.com/seffeng/
 * @copyright Copyright (c) 2022 seffeng
 */
namespace Seffeng\Nacos\Handlers;

use Seffeng\Nacos\Exceptions\NacosException;
use Seffeng\Nacos\Nacos;

class Instances
{
    /**
     * 服务实例IP
     * @var string
     */
    private $ip;

    /**
     * 服务实例port
     * @var integer
     */
    private $port;

    /**
     * 	命名空间ID
     * @var string
     */
    private $namespaceId;

    /**
     * 权重
     * @var float
     */
    private $weight;

    /**
     * 是否上线
     * @var boolean
     */
    private $enabled;

    /**
     * 是否健康
     * @var boolean
     */
    private $healthy;

    /**
     * 扩展信息
     * @var string
     */
    private $metadata;

    /**
     * 集群名
     * @var string
     */
    private $clusterName;

    /**
     * 服务名
     * @var string
     */
    private $serviceName;

    /**
     * 分组名
     * @var string
     */
    private $groupName;

    /**
     * 是否临时实例
     * @var boolean
     */
    private $ephemeral;

    /**
     * 是否只返回健康实例
     * @var boolean
     */
    private $healthyOnly = false;

    /**
     * 实例心跳内容[JSON格式字符串]
     * 示例：
     * {"ip":"127.0.0.1","port":80,"groupName":null,"serviceName":"local-server"}
     * @var string
     */
    private $beat;

    /**
     * 心跳间隔
     * @var integer
     */
    private $ttl = 3;

    /**
     * 注册实例|注销实例|修改实例|实例详情
     * 旧接口：/nacos/v1/ns/instance
     * @var string
     */
    private $uriInstance = '/nacos/v1/ns/upgrade/ops/instance';

    /**
     * 查询实例列表
     * 旧接口：/nacos/v1/ns/instance/list
     * @var string
     */
    private $uriInstanceList = '/nacos/v1/ns/upgrade/ops/instance/list';

    /**
     * 发送实例心跳
     * @var string
     */
    private $uriInstanceBeat = '/nacos/v1/ns/instance/beat';

    /**
     * 服务实例
     * @var mixed
     */
    private $instance;

    /**
     *
     * @var Nacos
     */
    private $nacos;

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param string $serviceName
     */
    public function __construct(Nacos $nacos, string $serviceName)
    {
        $this->nacos = $nacos;
        $this->setServiceName($serviceName);
    }

    /**
     * 注册实例
     * @author zxf
     * @date   2022年8月4日
     * @throws NacosException
     * @throws \Exception
     * @return boolean
     */
    public function register()
    {
        try {
            if (!$this->getServiceName() || !$this->getIp() || !$this->getPort()) {
                throw new NacosException('serviceName, ip, port required.');
            }
            $request = $this->getNacos()->getHttpClient()->post($this->getUriInstance(), [
                'form_params' => [
                    'ip' => $this->getIp(),
                    'port' => $this->getPort(),
                    'namespaceId' => $this->getNamespaceId(),
                    'weight' => $this->getWeight(),
                    'enabled' => $this->getEnabled(),
                    'healthy' => $this->getHealthy(),
                    'metadata' => $this->getMetadata(),
                    'clusterName' => $this->getClusterName(),
                    'serviceName' => $this->getServiceName(),
                    'groupName' => $this->getGroupName(),
                    'ephemeral' => $this->getEphemeral(),
                    'ver' => $this->getNacos()->getVersion(),
                    'username' => $this->getNacos()->getUsername(),
                    'password' => $this->getNacos()->getPassword()
                ]
            ]);
            if ($request->getStatusCode() === 200) {
                $body = $request->getBody()->getContents();
                if ($body === 'ok') {
                    return true;
                }
                return false;
            } else {
                throw new NacosException('instance register failed.');
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 实例详情
     * @author zxf
     * @date   2022年8月4日
     * @throws NacosException
     * @throws \Exception
     * @return boolean
     */
    public function detail()
    {
        try {
            if ($this->getInstance()) {
                return $this->getInstance();
            }
            if (!$this->getServiceName() || !$this->getIp() || !$this->getPort()) {
                throw new NacosException('serviceName, ip, port required.');
            }
            $request = $this->getNacos()->getHttpClient()->get($this->getUriInstance(), [
                'query' => [
                    'ip' => $this->getIp(),
                    'port' => $this->getPort(),
                    'groupName' => $this->getGroupName(),
                    'serviceName' => $this->getServiceName(),
                    'namespaceId' => $this->getNamespaceId(),
                    'cluster' => $this->getClusterName(),
                    'healthyOnly' => $this->getHealthyOnly(),
                    'ephemeral' => $this->getEphemeral(),
                    'ver' => $this->getNacos()->getVersion(),
                    'username' => $this->getNacos()->getUsername(),
                    'password' => $this->getNacos()->getPassword()
                ]
            ]);
            if ($request->getStatusCode() === 200) {
                $this->setInstance($request->getBody()->getContents());
                return $this->getInstance();
            } else {
                throw new NacosException('instance get failed.');
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 发送实例心跳
     * @author zxf
     * @date   2022年8月4日
     * @throws NacosException
     * @throws \Exception
     * @return boolean
     */
    public function beat()
    {
        $loop = 0;
        do {
            $loop++;
            $this->getLogger()->info('---InstancesBeatCount:---' . $loop . '--------');
            try {
                $request = $this->getNacos()->getHttpClient()->put($this->getUriInstanceBeat(), [
                    'query' => [
                        'ip' => $this->getIp(),
                        'port' => $this->getPort(),
                        'namespaceId' => $this->getNamespaceId(),
                        'weight' => $this->getWeight(),
                        'enabled' => $this->getEnabled(),
                        'healthy' => $this->getHealthy(),
                        'metadata' => $this->getMetadata(),
                        'clusterName' => $this->getClusterName(),
                        'serviceName' => $this->getServiceName(),
                        'groupName' => $this->getGroupName(),
                        'ephemeral' => $this->getEphemeral(),
                        'ver' => $this->getNacos()->getVersion(),
                        'username' => $this->getNacos()->getUsername(),
                        'password' => $this->getNacos()->getPassword(),
                        'beat' => $this->getBeat()
                    ]
                ]);
                if ($request->getStatusCode() === 200) {
                    $body = $request->getBody()->getContents();
                    $this->getLogger()->info($body);
                }
            } catch (\Exception $e) {
                $this->getLogger()->error($e->getMessage());
            }
            sleep($this->getTTL());
        } while (true);
    }

    /**
     * 实例列表
     * @author zxf
     * @date   2022年8月11日
     * @throws NacosException
     * @throws \Exception
     * @return mixed|NULL
     */
    public function list()
    {
        try {
            if (!$this->getServiceName()) {
                throw new NacosException('serviceName required.');
            }
            $request = $this->getNacos()->getHttpClient()->get($this->getUriInstanceList(), [
                'query' => [
                    'serviceName' => $this->getServiceName(),
                    'groupName' => $this->getGroupName(),
                    'namespaceId' => $this->getNamespaceId(),
                    'cluster' => $this->getClusterName(),
                    'healthyOnly' => $this->getHealthyOnly(),
                    'ver' => $this->getNacos()->getVersion(),
                    'username' => $this->getNacos()->getUsername(),
                    'password' => $this->getNacos()->getPassword()
                ]
            ]);
            if ($request->getStatusCode() === 200) {
                return $request->getBody()->getContents();
            } else {
                throw new NacosException('instance list failed.');
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param string $ip
     * @return static
     */
    public function setIp(string $ip)
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param integer $port
     * @return static
     */
    public function setPort(int $port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return integer
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月3日
     * @param string $namespaceId
     * @return static
     */
    public function setNamespaceId(string $namespaceId)
    {
        $this->namespaceId = $namespaceId;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月3日
     * @return string
     */
    public function getNamespaceId()
    {
        return $this->namespaceId;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param float $weight
     * @return static
     */
    public function setWeight(float $weight)
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return number
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param boolean $enabled
     * @return static
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * 若注册后状态异常，尝试传字符串 'true' 试试
     * @author zxf
     * @date   2022年8月4日
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param boolean $healthy
     * @return static
     */
    public function setHealthy($healthy)
    {
        $this->healthy = $healthy;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return boolean
     */
    public function getHealthy()
    {
        return $this->healthy;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param mixed $metadata
     * @return static
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return string
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param string $clusterName
     * @return static
     */
    public function setClusterName(string $clusterName)
    {
        $this->clusterName = $clusterName;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return string
     */
    public function getClusterName()
    {
        return $this->clusterName;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param string $serviceName
     * @return static
     */
    public function setServiceName(string $serviceName)
    {
        $this->serviceName = $serviceName;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return string
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param string $groupName
     * @return static
     */
    public function setGroupName(string $groupName)
    {
        $this->groupName = $groupName;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * 若注册时异常，尝试传字符串 'true' 或 空字符串 ''  或 null 试试
     * @author zxf
     * @date   2022年8月4日
     * @param boolean $ephemeral
     * @return static
     */
    public function setEphemeral($ephemeral)
    {
        $this->ephemeral = $ephemeral;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return string|boolean
     */
    public function getEphemeral()
    {
        return $this->ephemeral;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param boolean $healthyOnly
     * @return static
     */
    public function setHealthyOnly($healthyOnly)
    {
        $this->healthyOnly = $healthyOnly;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return string
     */
    public function getHealthyOnly()
    {
        return $this->healthyOnly;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param mixed $beat
     * @return static
     */
    public function setBeat($beat)
    {
        $this->beat = $beat;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return string
     */
    public function getBeat()
    {
        return $this->beat;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param int $ttl
     * @return static
     */
    public function setTTL(int $ttl)
    {
        $this->ttl = $ttl;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return number
     */
    public function getTTL()
    {
        return $this->ttl;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param string $uri
     * @return static
     */
    public function setUriInstance(string $uri)
    {
        $this->uriInstance = $uri;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return string
     */
    public function getUriInstance()
    {
        return $this->uriInstance;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param string $uri
     * @return static
     */
    public function setUriInstanceList(string $uri)
    {
        $this->uriInstanceList = $uri;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return string
     */
    public function getUriInstanceList()
    {
        return $this->uriInstanceList;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param mixed $beat
     * @return static
     */
    public function setUriInstanceBeat($beat)
    {
        $this->uriInstanceBeat = $beat;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return mixed
     */
    public function getUriInstanceBeat()
    {
        return $this->uriInstanceBeat;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return Nacos
     */
    public function getNacos()
    {
        return $this->nacos;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param  mixed $instance
     * @return static
     */
    public function setInstance($instance)
    {
        $this->instance = $instance;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return mixed|NULL
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return Log
     */
    public function getLogger()
    {
        return new Log();
    }
}
