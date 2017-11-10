<?php

namespace JF;

use Dotenv\Dotenv;
use GuzzleHttp\Client;

/**
 * 聚丰开放平台 API SDK
 *
 * @package JF
 * @author  yxz <yxz@jf.com>
 */
class JFOpenSDK
{
    /**
     * GuzzleHttp\Client 实例
     *
     * @var Client
     */
    protected $client;

    /**
     * API 版本
     *
     * @var string
     */
    protected $apiVersion = 'v1';

    /**
     * 初始化
     *
     * @param string $dotenvPath
     */
    public function __construct($dotenvPath)
    {
        (new Dotenv($dotenvPath))->load();

        $this->client = new Client([
            'base_uri' => getenv('JFOPEN_API_BASE_URL') . $this->apiVersion . '/',
            'timeout' => 10,
        ]);
    }

    /**
     * 获取待拉取的应用
     *
     * @return array
     */
    public function getApplications()
    {
        $token = $this->getToken();

        $response = $this->client->request('GET', 'applications', [
            'headers' => [
                'Authorization' => "Bearer {$token}",
            ],
        ]);
        $response_body = $response->getBody();
        $response_json = json_decode($response_body, true);

        return $response_json;
    }

    /**
     * 发布应用
     *
     * @param  int $applicationId 聚丰开放平台的应用ID
     * @param  string $publishUrl 已发布的应用访问地址
     * @param  int $dataId 已发布的应用在渠道方的数据ID
     * @return array
     */
    public function publishApplication($applicationId, $publishUrl, $dataId)
    {
        $token = $this->getToken();

        $response = $this->client->request('POST', 'applications/publish', [
            'headers' => [
                'Authorization' => "Bearer {$token}",
            ],
            'form_params' => [
                'id' => $applicationId,
                'url' => $publishUrl,
                'channel_data_id' => $dataId,
            ],
        ]);
        $response_body = $response->getBody();
        $response_json = json_decode($response_body, true);

        return $response_json;
    }

    /**
     * 拒绝应用
     *
     * @param  int $applicationId 聚丰开放平台的应用ID
     * @param  string $rejectMessage 拒绝的原因
     * @return array
     */
    public function rejectApplication($applicationId, $rejectMessage)
    {
        $token = $this->getToken();

        $response = $this->client->request('POST', 'applications/reject', [
            'headers' => [
                'Authorization' => "Bearer {$token}",
            ],
            'form_params' => [
                'id' => $applicationId,
                'reject_message' => $rejectMessage,
            ],
        ]);
        $response_body = $response->getBody();
        $response_json = json_decode($response_body, true);

        return $response_json;
    }

    /**
     * 获取令牌
     *
     * @return string
     */
    protected function getToken()
    {
        $response = $this->client->request('POST', 'token', [
            'form_params' => [
                'name' => getenv('JFOPEN_API_USERNAME'),
                'key' => getenv('JFOPEN_API_KEY'),
            ],
        ]);
        $response_body = $response->getBody();
        $response_json = json_decode($response_body, true);

        return $response_json['token'];
    }
}
