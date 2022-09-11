<?php

declare(strict_types=1);

namespace Zae\IndexNow\Jobs;

use Craft;
use craft\elements\Entry;
use craft\queue\BaseJob;
use craft\queue\QueueInterface;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use yii\queue\Queue;

class PingJob extends BaseJob
{
    private const INDEXNOW_BASEURI = 'https://api.indexnow.org';
    private const INDEXNOW_PATH    = '/indexnow';

    public int $entry;

    /**
     * @param Queue|QueueInterface $queue The queue the job belongs to
     *
     * @return bool
     * @throws Exception
     */
    public function execute($queue): bool
    {
        $entryModel = Entry::find()->site('*')->id($this->entry)->one();

        return $this->sendPing($entryModel);
    }

    /**
     * @inheritdoc
     */
    protected function defaultDescription(): string
    {
        return "IndexNow ping: #{$this->entry}";
    }

    /**
     * @param Entry $entry
     *
     * @return void
     * @throws Exception|\GuzzleHttp\Exception\GuzzleException
     */
    private function sendPing(Entry $entry): bool
    {
        $client = new Client([
            'base_uri' => self::INDEXNOW_BASEURI,
            RequestOptions::HTTP_ERRORS => true
        ]);

        try {
            $response = $client->get(self::INDEXNOW_PATH, [
                'query' => [
                    'url' => $entry->getUrl(),
                    'key' => Craft::$app->config->general->indexNowKey,
                ]
            ]);

            switch ($response->getStatusCode()) {
                case 200:
                case 202:
                    return true;
                default:
                    $body = $this->decodeResponse($response);

                    throw new Exception($body->message);
            }
        } catch (RequestException $e) {
            $body = $this->decodeResponse($e->getResponse());

            throw new Exception($body->message, 500, $e);
        }
    }

    /**
     * @param ResponseInterface $response
     *
     * @return object
     * @throws \JsonException
     */
    private function decodeResponse(ResponseInterface $response): object
    {
        return json_decode((string)$response->getBody(), false, 512, JSON_THROW_ON_ERROR);
    }
}
