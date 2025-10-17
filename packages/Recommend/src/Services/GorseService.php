<?php

namespace Packages\Recommend\Services;

use Carbon\Carbon;
use GuzzleHttp;
use GuzzleHttp\Exception\GuzzleException;
use JsonSerializable;

class GorseUser implements JsonSerializable
{
    public string $userId;

    public array $labels;

    public string $comment;

    public array $subscribe;

    public function __construct(string $userId, array $labels, array $subscribe, string $comment)
    {
        $this->userId = $userId;
        $this->labels = $labels;
        $this->subscribe = $subscribe;
        $this->comment = $comment;
    }

    public function jsonSerialize(): array
    {
        return [
            'UserId' => $this->userId,
            'Labels' => $this->labels,
            'Subscribe' => $this->subscribe,
            'Comment' => $this->comment,
        ];
    }

    public static function fromJSON($json): GorseUser
    {
        return new GorseUser($json['UserId'], $json['Labels'], $json['Subscribe'], $json['Comment']);
    }
}

class GorseItem implements JsonSerializable
{
    public string $itemId;

    public array $labels;

    public array $categories;

    public string $comment;

    public bool $isHidden;

    public string $timestamp;

    public function __construct(string $itemId, array $labels, array $categories, string $comment, bool $isHidden, ?string $timestamp = null)
    {
        $this->itemId = $itemId;
        $this->labels = $labels;
        $this->categories = $categories;
        $this->comment = $comment;
        $this->isHidden = $isHidden;
        $this->timestamp = $timestamp ?? Carbon::now()->toDateTimeString();
    }

    public function jsonSerialize(): array
    {
        return [
            'ItemId' => $this->itemId,
            'Labels' => $this->labels,
            'Categories' => $this->categories,
            'Comment' => $this->comment,
            'IsHidden' => $this->isHidden,
            'Timestamp' => $this->timestamp,
        ];
    }

    public static function fromJSON($json): GorseItem
    {
        return new GorseItem($json['ItemId'], $json['Labels'], $json['Categories'], $json['Comment'], $json['IsHidden'], $json['Timestamp']);
    }
}

class Feedback implements JsonSerializable
{
    public string $feedbackType;

    public string $userId;

    public string $itemId;

    public string $comment;

    public string $timestamp;

    public function __construct(string $feedbackType, string $userId, string $itemId, string $comment, string $timestamp)
    {
        $this->feedbackType = $feedbackType;
        $this->userId = $userId;
        $this->itemId = $itemId;
        $this->comment = $comment;
        $this->timestamp = $timestamp;
    }

    public function jsonSerialize(): array
    {
        return [
            'FeedbackType' => $this->feedbackType,
            'UserId' => $this->userId,
            'ItemId' => $this->itemId,
            'Comment' => $this->comment,
            'Timestamp' => $this->timestamp,
        ];
    }
}

class RowAffected
{
    public int $rowAffected;

    public static function fromJSON($json): RowAffected
    {
        $rowAffected = new RowAffected();
        $rowAffected->rowAffected = $json['RowAffected'];

        return $rowAffected;
    }
}

final class GorseService
{
    private string $endpoint;

    private ?string $apiKey;

    public function __construct()
    {
        $this->endpoint = config('recommend.endpoint');
        $this->apiKey = config('recommend.apiKey');
    }

    /**
     * @throws GuzzleException
     */
    public function insertUser(GorseUser $user): RowAffected
    {
        return RowAffected::fromJSON($this->request('POST', '/api/user', $user));
    }

    /**
     * @throws GuzzleException
     */
    public function updateUser(GorseUser $user): RowAffected
    {
        $path = '/api/user/'.rawurlencode($user->userId);

        return RowAffected::fromJSON($this->request('PATCH', $path, $user));
    }

    /**
     * @throws GuzzleException
     */
    public function getUser(string $user_id): GorseUser
    {
        $path = '/api/user/'.rawurlencode($user_id);

        return GorseUser::fromJSON($this->request('GET', $path, null));
    }

    /**
     * @throws GuzzleException
     */
    public function deleteUser(string $user_id): RowAffected
    {
        $path = '/api/user/'.rawurlencode($user_id);

        return RowAffected::fromJSON($this->request('DELETE', $path, null));
    }

    /**
     * @throws GuzzleException
     */
    public function insertItem(GorseItem $item): RowAffected
    {
        return RowAffected::fromJSON($this->request('POST', '/api/item', $item));
    }

    /**
     * @throws GuzzleException
     */
    public function updateItem(GorseItem $item): RowAffected
    {
        $path = '/api/item/'.rawurlencode($item->itemId);

        return RowAffected::fromJSON($this->request('PATCH', $path, $item));
    }

    /**
     * @throws GuzzleException
     */
    public function getItem(string $item_id): GorseItem
    {
        $path = '/api/item/'.rawurlencode($item_id);

        return GorseItem::fromJSON($this->request('GET', $path, null));
    }

    /**
     * @throws GuzzleException
     */
    public function deleteItem(string $item_id): RowAffected
    {
        $path = '/api/item/'.rawurlencode($item_id);

        return RowAffected::fromJSON($this->request('DELETE', $path, null));
    }

    /**
     * @throws GuzzleException
     */
    public function insertItemCategory(string $item_id, string $category_id): RowAffected
    {
        $path = sprintf(
            '/api/item/%s/category/%s',
            rawurlencode($item_id),
            rawurlencode($category_id)
        );

        return RowAffected::fromJSON($this->request('PUT', $path, null));
    }

    /**
     * @throws GuzzleException
     */
    public function deleteItemCategory(string $item_id, string $category_id): RowAffected
    {
        $path = sprintf(
            '/api/item/%s/category/%s',
            rawurlencode($item_id),
            rawurlencode($category_id)
        );

        return RowAffected::fromJSON($this->request('DELETE', $path, null));
    }

    /**
     * @throws GuzzleException
     */
    public function insertFeedback(Feedback $feedback): RowAffected
    {
        return RowAffected::fromJSON($this->request('POST', '/api/feedback', [$feedback]));
    }

    /**
     * @throws GuzzleException
     */
    public function deleteFeedback(string $type, string $user_id, string $item_id): RowAffected
    {
        $path = sprintf(
            '/api/feedback/%s/%s/%s',
            rawurlencode($type),
            rawurlencode($user_id),
            rawurlencode($item_id)
        );

        return RowAffected::fromJSON($this->request('DELETE', $path, null));
    }

    /**
     * @throws GuzzleException
     */
    public function getRecommend(string $user_id, int $n, int $offset)
    {
        $query = [
            'n' => $n,
            'offset' => $offset,
        ];

        $path = sprintf('/api/recommend/%s?%s', rawurlencode($user_id), http_build_query($query));

        return $this->request('GET', $path, null);
    }

    /**
     * @throws GuzzleException
     */
    public function getRecommendByCategory(string $user_id, int $n, int $offset, array $categories)
    {
        $query = [
            'n' => $n,
            'offset' => $offset,
        ];

        $queryString = http_build_query($query);
        foreach ($categories as $category) {
            $queryString .= '&category='.rawurlencode($category);
        }

        $path = sprintf('/api/recommend/%s?%s', rawurlencode($user_id), $queryString);

        return $this->request('GET', $path, null);
    }

    /**
     * @throws GuzzleException
     */
    public function getNonPersonalizedRecommend(string $name, int $n, int $offset)
    {
        $query = [
            'n' => $n,
            'offset' => $offset,
        ];

        $path = sprintf('/api/non-personalized/%s?%s', rawurlencode($name), http_build_query($query));

        return $this->request('GET', $path, null);
    }

    /**
     * @throws GuzzleException
     */
    public function getNonPersonalizedRecommendByCategory(string $name, int $n, int $offset, array $categories)
    {
        $query = [
            'n' => $n,
            'offset' => $offset,
        ];

        $queryString = http_build_query($query);
        foreach ($categories as $category) {
            $queryString .= '&category='.rawurlencode($category);
        }

        $path = sprintf('/api/non-personalized/%s?%s', rawurlencode($name), $queryString);

        return $this->request('GET', $path, null);
    }

    /**
     * @throws GuzzleException
     */
    private function request(string $method, string $uri, $body)
    {
        try {
            $client = new GuzzleHttp\Client(['base_uri' => $this->endpoint]);
            $options = [];
            if ($this->apiKey) {
                $options[GuzzleHttp\RequestOptions::HEADERS] = ['X-API-Key' => $this->apiKey];
            }
            if ($body != null) {
                $options[GuzzleHttp\RequestOptions::JSON] = $body;
            }

            $response = $client->request($method, $uri, $options);
            $statusCode = $response->getStatusCode();
            $content = (string) $response->getBody();

            return json_decode($content, true);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $response = $e->getResponse();
            $statusCode = $response ? $response->getStatusCode() : 'N/A';
            $content = $response ? (string) $response->getBody() : $e->getMessage();

            error_log("Gorse API Error ({$statusCode}): {$content}");

            return;
        }
    }
}
