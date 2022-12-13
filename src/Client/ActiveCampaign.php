<?php

namespace Kingscode\ActiveCampaignApi\Client;

use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Testing\Assert;
use Kingscode\ActiveCampaignApi\Log\Log;

class ActiveCampaign
{
    public const RATE_LIMIT_KEY = 'active-campaing-calls';

    public const RATE_LIMIT_DECAY = 1;

    public const RATE_LIMIT = 5;

    private string $baseUri;

    private array $baseHeaders;

    public function __construct(string $url, string $apiKey, ?int $version = 3)
    {
        Assert::assertNotEmpty($url, 'Make sure the Active Campaign URL is set in your ENV');
        Assert::assertNotEmpty($apiKey, 'Make sure the Active Campaign API key is set in your ENV');

        $this->baseUri = sprintf('%s/api/%d/', $url, $version);

        $this->baseHeaders = [
            'Api-Token'    => $apiKey,
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ];
    }

    /**
     * @param  string $url
     * @param  array  $queryParams
     * @return \Illuminate\Http\Client\Response
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function link(string $url): array
    {
        return $this->rateLimitedCall(
            function () use ($url, $queryParams) {
                $response = Http::withHeaders($this->baseHeaders)
                    ->retry(3, 2000,  function ($exception) {
                        return $this->validateWhenRetry($exception);
                    })
                    ->get($url);

                return $response?->json() ?: [];
            }
        );
    }

    /**
     * @param  string $url
     * @param  array  $queryParams
     * @return \Illuminate\Http\Client\Response
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function get(string $url, array $queryParams = []): array
    {
        return $this->rateLimitedCall(
            function () use ($url, $queryParams) {
                $response = Http::withHeaders($this->baseHeaders)
                    ->retry(3, 2000,  function ($exception) {
                        return $this->validateWhenRetry($exception);
                    })
                    ->get(sprintf('%s%s', $this->baseUri, $url), $queryParams);

                return $response?->json() ?: [];
            }
        );
    }

    /**
     * @param  string $url
     * @param  array  $data
     * @param  array  $queryParams
     * @return \Illuminate\Http\Client\Response
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function post(string $url, array $data = [], array $queryParams = []): array
    {
        return $this->rateLimitedCall(
            function () use ($url, $data) {
                $response = Http::withHeaders($this->baseHeaders)
                    ->retry(3, 2000, function ($exception) {
                        return $this->validateWhenRetry($exception);
                    })
                    ->post(sprintf('%s%s', $this->baseUri, $url), $data);

                return $response?->json() ?: [];
            }
        );
    }

    /**
     * @param  string $url
     * @param  array  $data
     * @param  array  $queryParams
     * @return \Illuminate\Http\Client\Response
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function put(string $url, array $data = []): array
    {
        return $this->rateLimitedCall(
            function () use ($url, $data) {
                $response = Http::withHeaders($this->baseHeaders)
                    ->retry(3, 20, function ($exception) {
                        return $this->validateWhenRetry($exception);
                    })
                    ->put(sprintf('%s%s', $this->baseUri, $url), $data);

                return $response?->json() ?: [];
            }
        );
    }

    /**
     * @param  string $url
     * @param  array  $data
     * @param  array  $queryParams
     * @return \Illuminate\Http\Client\Response
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function delete(string $url, array $data = []): array
    {
        return $this->rateLimitedCall(
            function () use ($url, $data) {
                $response = Http::withHeaders($this->baseHeaders)
                    ->retry(3, 2000, function ($exception) {
                        return $this->validateWhenRetry($exception);
                    })
                    ->delete(sprintf('%s%s', $this->baseUri, $url), $data);

                return $response?->json() ?: [];
            }
        );
    }

    /**
     * @return int
     */
    public function resetRateLimit()
    {
        RateLimiter::resetAttempts(self::RATE_LIMIT_KEY);
    }

    /**
     * @return int
     */
    public function getRemainingCalls(): int
    {
        return RateLimiter::remaining(self::RATE_LIMIT_KEY, self::RATE_LIMIT);
    }

    /**
     * @return int
     */
    public function getAvailableIn(): string
    {
        return sprintf('You may try again in %d seconds', RateLimiter::availableIn(self::RATE_LIMIT_KEY));
    }

    public function validateWhenRetry(?Exception $exception)
    {
        switch ($exception->getCode()) {
            case 429:
                return true;
            break;
            default:
                return false;
        }
    }

    /**
     * Rate limited call.
     *
     * @param  callable $call
     * @return null
     */
    private function rateLimitedCall(callable $call)
    {
        $response = [];
        try {
            if (RateLimiter::remaining(self::RATE_LIMIT_KEY, self::RATE_LIMIT) > 0) {
                RateLimiter::hit(self::RATE_LIMIT_KEY, self::RATE_LIMIT_DECAY);

                $response = $call();
            }
        } catch (Exception|RequestException $e) {
            Log::activecampaign()->error($e->getMessage() . ' => ' . $e->getTraceAsString());
            throw $e;
        } finally {
            if (RateLimiter::tooManyAttempts(self::RATE_LIMIT_KEY, self::RATE_LIMIT)) {
                Log::activecampaign()->warning($this->getAvailableIn());
            }
        }

        return $response;
    }
}
