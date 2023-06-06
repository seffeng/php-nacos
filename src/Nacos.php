<?php
/**
 * @link http://github.com/seffeng/
 * @copyright Copyright (c) 2022 seffeng
 */
namespace Seffeng\Nacos;

use GuzzleHttp\Client;

/**
 *
 * @author zxf
 * @date   2022年8月5日
 * @see    https://nacos.io
 */
class Nacos
{
    /**
     * Nacos服务地址
     * @var string
     */
    private $host;

    /**
     *
     * @var string
     */
    private $username;

    /**
     *
     * @var string
     */
    private $password;

    /**
     *
     * @var integer
     */
    private $timeout;

    /**
     * 版本 v1 或者 v2
     * @var string
     */
    private $version;

    /**
     *
     * @var Client
     */
    private $httpClient;

    /**
     *
     * @author zxf
     * @date   2022年8月3日
     */
    public function __construct(string $host, string $username = null, string $password = null, int $timeout = 10)
    {
        $this->setHost($host)->setUsername($username)->setPassword($password)->setTimeout($timeout);
    }

    /**
     *
     * @author zxf
     * @date   2022年9月16日
     * @return array
     */
    public function getAuthoriseParameter()
    {
        return [
            'username' => $this->getUsername(),
            'password' => $this->getPassword()
        ];
    }

    /**
     *
     * @author zxf
     * @date   2022年8月3日
     * @param string $host
     * @return static
     */
    public function setHost(string $host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月3日
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月3日
     * @param string $username
     * @return static
     */
    public function setUsername(string $username = null)
    {
        $this->username = $username;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月3日
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月3日
     * @param string $password
     * @return static
     */
    public function setPassword(string $password = null)
    {
        $this->password = $password;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月3日
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月3日
     * @param integer $timeout
     * @return static
     */
    public function setTimeout(int $timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月3日
     * @return integer
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月6日
     * @param string $version
     * @return static
     */
    public function setVersion(string $version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月6日
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     *
     * @author zxf
     * @date   2022年8月3日
     * @return Client
     */
    public function getHttpClient()
    {
        return new Client(['base_uri' => $this->getHost(), 'timeout' => $this->getTimeout()]);
    }
}
