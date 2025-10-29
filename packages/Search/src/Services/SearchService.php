<?php

namespace Packages\Search\Services;

use Meilisearch\Client;
use Meilisearch\Contracts\SearchQuery;

class SearchService
{
    protected $client;
    protected static array $handlers = [];

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Register handler
     * 
     * @param string $index
     * @param callable $callback
     * @param array<int, string> $searchableAttributes
     * @param array<int, string> $filterableAttributes
     * @param array<int, string> $sortableAttributes
     * @return void
     */
    public static function registerHandler(string $index, callable $callback, array $searchableAttributes, array $filterableAttributes, array $sortableAttributes): void
    {
        self::$handlers[$index] = $callback;

        $indexSettings = config('scout.meilisearch.index-settings', []);
        $indexSettings[$index] = [
            'searchableAttributes' => $searchableAttributes,
            'filterableAttributes' => $filterableAttributes,
            'sortableAttributes'   => $sortableAttributes,
        ];
        config(['scout.meilisearch.index-settings' => $indexSettings]);
    }

    /**
     * Return all registered handlers
     * 
     * @return array<string, callable>
     */
    public static function getHandlers(): array
    {
        return self::$handlers;
    }

    /**
     * Search accross multiple handlers
     * 
     * @param string $query
     * @return array<int, array<string, mixed>>
     */
    public function search(string $query): array
    {
        $searchQueries = [];

        foreach (array_keys(self::$handlers) as $index) {
            $searchQueries[] = (new SearchQuery())->setIndexUid($index)->setQuery($query);
        }

        $results = $this->client->multiSearch($searchQueries);
        $response = [];

        foreach ($results['results'] as $indexResult) {
            $indexUid = $indexResult['indexUid'];
            $hits = $indexResult['hits'] ?? [];

            if (isset(self::$handlers[$indexUid])) {
                foreach ($hits as $hit) {
                    $response[] = call_user_func(self::$handlers[$indexUid], $hit);
                }
            } else {
                $response[] = $hits;
            }
        }

        return $response;
    }
}
