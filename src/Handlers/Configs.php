<?php
/**
 * @link http://github.com/seffeng/
 * @copyright Copyright (c) 2022 seffeng
 */
namespace Seffeng\Nacos\Handlers;

use Seffeng\Nacos\Nacos;
use Seffeng\Nacos\Exceptions\NacosException;

class Configs
{
    /**
     * 租户信息，对应 Nacos 的命名空间ID字段
     * tenant
     * @var string
     */
    private $namespaceId;

    /**
     * 配置 ID
     * @var string
     */
    private $dataId;

    /**
     * 配置分组
     * @var string
     */
    private $groupName;

    /**
     * 监听数据报文。格式为 dataId^2Group^2contentMD5^2tenant^1或者dataId^2Group^2contentMD5^1。
     * dataId：配置 ID
     * group：配置分组
     * contentMD5：配置内容 MD5 值
     * tenant：租户信息，对应 Nacos 的命名空间字段(非必填)
     * @var string
     */
    private $listeningConfigs;

    /**
     *  配置内容
     * @var string
     */
    private $content;

    /**
     * 长轮训等待 30s，此处填写 30000
     * @var integer
     */
    private $longPullingTimeout = 30000;

    /**
     * 当前页码
     * @var integer
     */
    private $page;

    /**
     * 分页条数(默认100条,最大为500)
     * @var integer
     */
    private $pageSize;

    /**
     * 配置ID|配置项历史版本ID
     * @var integer
     */
    private $id;

    /**
     * 配置类型
     * @var string
     */
    private $type;

    /**
     * 配置文件路径
     * @var string
     */
    private $filePath = 'nacos/config';

    /**
     * 配置文件名
     * @var string
     */
    private $fileName;

    /**
     * 配置文件通过命名空间ID隔离
     * @var boolean
     */
    private $isSeparate = true;

    /**
     *
     * @var Nacos
     */
    private $nacos;

    /**
     * 获取配置
     * @var string
     */
    private $uriGetConfig = '/nacos/v1/cs/configs';

    /**
     * 监听配置
     * @var string
     */
    private $uriListenerConfig = '/nacos/v1/cs/configs/listener';

    /**
     *
     * @author zxf
     * @date   2022年8月3日
     * @param Nacos $nacos
     * @param string $dataId
     * @param string $groupName
     */
    public function __construct(Nacos $nacos, string $dataId, string $groupName = 'DEFAULT_GROUP')
    {
        $this->nacos = $nacos;
        $this->setDataId($dataId)->setGroupName($groupName);
        $this->setFileName();
    }

    /**
     *
     * @author zxf
     * @date   2022年8月3日
     * @throws NacosException
     * @throws \Exception
     */
    public function getConfig()
    {
        try {
            $request = $this->getNacos()->getHttpClient()->get($this->getUriGetConfig(), [
                'query' => [
                    'dataId' => $this->getDataId(),
                    'group' => $this->getGroupName(),
                    'tenant' => $this->getNamespaceId(),
                    'username' => $this->getNacos()->getUsername(),
                    'password' => $this->getNacos()->getPassword()
                ]
            ]);
            if ($request->getStatusCode() === 200) {
                $this->setContent($request->getBody()->getContents());
                $this->getLogger()->info($this->content);
                $this->saveFile();
                return $this->getContent();
            } else {
                throw new NacosException('config get failed.');
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     */
    public function listenerConfig()
    {
        $loop = 0;
        do {
            $loop++;
            $this->getLogger()->info('---listenerConfigCount:---' . $loop . '--------');
            try {
                $this->setListeningConfigs($this->getDataId() . $this->twoEncode() . $this->getGroupName() . $this->twoEncode() . md5($this->getContent())
                                        . ($this->getNamespaceId() ? ($this->twoEncode() . $this->getNamespaceId() . $this->oneEncode()) : $this->oneEncode()));
                $request = $this->getNacos()->setTimeout(0)->getHttpClient()->post($this->getUriListenerConfig(), [
                    'form_params' => [
                        'Listening-Configs' => $this->getListeningConfigs(),
                        'username' => $this->getNacos()->getUsername(),
                        'password' => $this->getNacos()->getPassword()
                    ],
                    'headers' => [
                        'Long-Pulling-Timeout' => $this->getLongPullingTimeout()
                    ]
                ]);
                if ($request->getBody()->getContents()) {
                    $this->getConfig();
                }
            } catch (\Exception $e) {
                $this->getLogger()->error($e->getMessage());
            }
            sleep(1);
        } while (true);
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @throws \Exception
     */
    public function saveFile()
    {
        try {
            $filename = $this->getFilePath() . DIRECTORY_SEPARATOR . $this->getFileName();
            if (file_exists($filename)) {
                @unlink($filename);
            }
            if (!is_dir($this->getFilePath())) {
                mkdir($this->getFilePath(), 0777, true);
            }
            file_put_contents($filename, $this->content);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     *
     * @author zxf
     * @date   2022年8月3日
     * @param int $time
     * @return static
     */
    public function setLongPullingTimeout(int $time)
    {
        $this->longPullingTimeout = $time * 1000;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月3日
     * @return number
     */
    public function getLongPullingTimeout()
    {
        return $this->longPullingTimeout;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月3日
     * @return Nacos
     */
    public function getNacos()
    {
        return $this->nacos;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月3日
     * @param string $dataId
     * @return static
     */
    public function setDataId(string $dataId)
    {
        $this->dataId = $dataId;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月3日
     * @return string
     */
    public function getDataId()
    {
        return $this->dataId;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月3日
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
     * @date   2022年8月3日
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupName;
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
     * @date   2022年8月3日
     * @return string
     */
    public function twoEncode()
    {
        return pack('C*', 2);
    }

    /**
     *
     * @author zxf
     * @date   2022年8月3日
     * @return string
     */
    public function oneEncode()
    {
        return pack('C*', 1);
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param string $filepPath
     * @return static
     */
    public function setFilePath(string $filepPath)
    {
        $this->filePath = $filepPath;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath . ($this->getIsSeparate() ? (DIRECTORY_SEPARATOR . $this->getNamespaceId()) : '');
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param boolean $isSeparate
     * @return static
     */
    public function setIsSeparate(bool $isSeparate = true)
    {
        $this->isSeparate = $isSeparate;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return boolean
     */
    public function getIsSeparate()
    {
        return $this->isSeparate;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param string $fileName
     * @return static
     */
    public function setFileName(string $fileName = null)
    {
        !is_null($fileName) && $this->fileName = $fileName;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return string
     */
    public function getFileName()
    {
        return is_null($this->fileName) ? $this->getDataId() : $this->fileName;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param  string $uri
     * @return static
     */
    public function setUriGetConfig(string $uri)
    {
        $this->uriGetConfig = $uri;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return string
     */
    public function getUriGetConfig()
    {
        return $this->uriGetConfig;
    }
    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param  string $uri
     * @return static
     */
    public function setUriListenerConfig(string $uri)
    {
        $this->uriListenerConfig = $uri;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return string
     */
    public function getUriListenerConfig()
    {
        return $this->uriListenerConfig;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param string $content
     * @return static
     */
    public function setListeningConfigs(string $content)
    {
        $this->listeningConfigs = $content;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return string
     */
    public function getListeningConfigs()
    {
        return $this->listeningConfigs;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param string $content
     * @return static
     */
    public function setContent(string $content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param integer $page
     * @return static
     */
    public function setPage(int $page)
    {
        $this->page = $page;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return integer
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param integer $pageSize
     * @return static
     */
    public function setPageSize(int $pageSize)
    {
        $this->pageSize = $pageSize;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return integer
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param integer $page
     * @return static
     */
    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @param string $type
     * @return static
     */
    public function setType(string $type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月4日
     * @return Log
     */
    private function getLogger()
    {
        return new Log();
    }
}
