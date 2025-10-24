<?php

namespace Packages\Recommend\Services;

use GuzzleHttp;
use GuzzleHttp\Exception\GuzzleException;
use Packages\Recommend\Classes\GorseFeedback;
use Packages\Recommend\Classes\GorseItem;
use Packages\Recommend\Classes\GorseUser;
use Packages\Recommend\Classes\RowAffected;

class GorseService
{
    private string $endpoint;

    private ?string $apiKey;

    public function __construct()
    {
        $this->endpoint = config('recommend.endpoint');
        $this->apiKey   = config('recommend.apiKey');
    }

    /**
     * Insert a user to Gorse.
     *
     * @throws GuzzleException
     */
    public function insertUser(GorseUser $user): RowAffected
    {
        return RowAffected::fromJSON($this->request('POST', '/api/user', $user));
    }

    /**
     * Update user from Gorse.
     *
     * @throws GuzzleException
     */
    public function updateUser(GorseUser $user): RowAffected
    {
        $path = '/api/user/' . rawurlencode($user->getUserId());

        return RowAffected::fromJSON($this->request('PATCH', $path, $user));
    }

    /**
     * Get user from Gorse.
     *
     * @throws GuzzleException
     */
    public function getUser(string $userId): GorseUser
    {
        $path = '/api/user/' . rawurlencode($userId);

        return GorseUser::fromJSON($this->request('GET', $path, null));
    }

    /**
     * Delete user from Gorse.
     *
     * @throws GuzzleException
     */
    public function deleteUser(string $userId): RowAffected
    {
        $path = '/api/user/' . rawurlencode($userId);

        return RowAffected::fromJSON($this->request('DELETE', $path, null));
    }

    /**
     * Insert item to Gorse.
     *
     * @throws GuzzleException
     */
    public function insertItem(GorseItem $item): RowAffected
    {
        return RowAffected::fromJSON($this->request('POST', '/api/item', $item));
    }

    /**
     * Update item from Gorse.
     *
     * @throws GuzzleException
     */
    public function updateItem(GorseItem $item): RowAffected
    {
        $path = '/api/item/' . rawurlencode($item->getItemId());

        return RowAffected::fromJSON($this->request('PATCH', $path, $item));
    }

    /**
     * Get item from Gorse.
     *
     * @throws GuzzleException
     */
    public function getItem(string $itemId): GorseItem
    {
        $path = '/api/item/' . rawurlencode($itemId);

        return GorseItem::fromJSON($this->request('GET', $path, null));
    }

    /**
     * Delete item from Gorse.
     *
     * @throws GuzzleException
     */
    public function deleteItem(string $itemId): RowAffected
    {
        $path = '/api/item/' . rawurlencode($itemId);

        return RowAffected::fromJSON($this->request('DELETE', $path, null));
    }

    /**
     * Insert category to item.
     *
     * @throws GuzzleException
     */
    public function insertItemCategory(string $itemId, string $categoryId): RowAffected
    {
        $path = sprintf(
            '/api/item/%s/category/%s',
            rawurlencode($itemId),
            rawurlencode($categoryId)
        );

        return RowAffected::fromJSON($this->request('PUT', $path, null));
    }

    /**
     * Delete category from item.
     *
     * @throws GuzzleException
     */
    public function deleteItemCategory(string $itemId, string $categoryId): RowAffected
    {
        $path = sprintf(
            '/api/item/%s/category/%s',
            rawurlencode($itemId),
            rawurlencode($categoryId)
        );

        return RowAffected::fromJSON($this->request('DELETE', $path, null));
    }

    /**
     * Inert feedback to Gorse.
     *
     * @throws GuzzleException
     */
    public function insertFeedback(GorseFeedback $feedback): RowAffected
    {
        return RowAffected::fromJSON($this->request('POST', '/api/feedback', [$feedback]));
    }

    /**
     * Delete feedback from Gorse.
     *
     * @throws GuzzleException
     */
    public function deleteFeedback(string $type, string $userId, string $itemId): RowAffected
    {
        $path = sprintf(
            '/api/feedback/%s/%s/%s',
            rawurlencode($type),
            rawurlencode($userId),
            rawurlencode($itemId)
        );

        return RowAffected::fromJSON($this->request('DELETE', $path, null));
    }

    /**
     * Get personalized recommendations for a user.
     *
     * @return array<int, string>|null
     *
     * @throws GuzzleException
     */
    public function getRecommend(string $userId, int $n, int $offset): ?array
    {
        $query = [
            'n'      => $n,
            'offset' => $offset,
        ];

        $path = sprintf('/api/recommend/%s?%s', rawurlencode($userId), http_build_query($query));

        return $this->request('GET', $path, null);
    }

    /**
     * Get personalized recommendations for a user filtered by categories.
     *
     * @param array<int, string> $categories
     *
     * @return array<int, string>|null
     *
     * @throws GuzzleException
     */
    public function getRecommendByCategory(string $userId, int $n, int $offset, array $categories): ?array
    {
        $query = [
            'n'      => $n,
            'offset' => $offset,
        ];

        $queryString = http_build_query($query);
        foreach ($categories as $category) {
            $queryString .= '&category=' . rawurlencode($category);
        }

        $path = sprintf('/api/recommend/%s?%s', rawurlencode($userId), $queryString);

        return $this->request('GET', $path, null);
    }

    /**
     * Get non-personalized recommendations.
     *
     * @return array<int, string>|null
     *
     * @throws GuzzleException
     */
    public function getNonPersonalizedRecommend(string $name, int $n, int $offset): ?array
    {
        $query = [
            'n'      => $n,
            'offset' => $offset,
        ];

        $path = sprintf('/api/non-personalized/%s?%s', rawurlencode($name), http_build_query($query));

        return $this->request('GET', $path, null);
    }

    /**
     * Get non-personalized recommendations filtered by categories.
     *
     * @param array<int, string> $categories
     *
     * @return array<int, string>|null
     *
     * @throws GuzzleException
     */
    public function getNonPersonalizedRecommendByCategory(string $name, int $n, int $offset, array $categories): ?array
    {
        $query = [
            'n'      => $n,
            'offset' => $offset,
        ];

        $queryString = http_build_query($query);
        foreach ($categories as $category) {
            $queryString .= '&category=' . rawurlencode($category);
        }

        $path = sprintf('/api/non-personalized/%s?%s', rawurlencode($name), $queryString);

        return $this->request('GET', $path, null);
    }

    /**
     * Send HTTP request to Gorse API.
     *
     * @return mixed|null
     *
     * @throws GuzzleException
     */
    private function request(string $method, string $uri, mixed $body): mixed
    {
        try {
            $client  = new GuzzleHttp\Client(['base_uri' => $this->endpoint]);
            $options = [];
            if ($this->apiKey) {
                $options[GuzzleHttp\RequestOptions::HEADERS] = ['X-API-Key' => $this->apiKey];
            }
            if ($body !== null) {
                $options[GuzzleHttp\RequestOptions::JSON] = $body;
            }

            $response   = $client->request($method, $uri, $options);
            $statusCode = $response->getStatusCode();
            $content    = (string) $response->getBody();

            return json_decode($content, true);
        } catch (GuzzleHttp\Exception\RequestException $e) {
            $response   = $e->getResponse();
            $statusCode = $response ? $response->getStatusCode() : 'N/A';
            $content    = $response ? (string) $response->getBody() : $e->getMessage();

            logger()->error("Gorse API Error ({$statusCode}): {$content}", ['e' => $e]);

            return null;
        } catch (\Exception $e) {
            logger()->error('Gorse API Error', ['e' => $e]);

            return null;
        }
    }
}
