<?php
/**
 * @author Yenier Jimenez <yjmorales86@gmail.com>
 */

namespace App\Core\Azure\Authentication;

use Common\DataStorage\Redis\RedisCacheRegistry;
use Exception;

/**
 * Cache used to save temporary information to communicate with Azure AD .
 */
abstract class AbstractAzureAuthenticatorCache
{
    /**
     * Prefix used as part of the key to save the Azure AD Authentication using Bearer tokens respective to Azure
     * Services.
     */
    private const AZURE_AUTHENTICATOR_CACHE = 'azure-active-directory-authentication-token-key-';

    /**
     * Redis cache connection handler used to save and retrieve information.
     *
     * @var RedisCacheRegistry
     */
    protected RedisCacheRegistry $_cache;

    /**
     * @param RedisCacheRegistry $cache Redis cache connection handler used to save and retrieve information.
     */
    public function __construct(RedisCacheRegistry $cache)
    {
        $this->_cache = $cache;
    }

    /**
     * Use this function to get from cache the Azure Active Directory Bearer token to authenticate the security
     * principal for a time period
     *
     * @return string|null
     */
    public function getAuthToken(): ?string
    {
        $found = true;
        $token = $this->_cache->get($this->_getAuthTokenKey(), $found);

        return $found ? $token : null;
    }

    /**
     * Use this function to save into cache the Azure Active Directory Bearer token to authenticate the
     * security principal.
     *
     * @param string $token Token to be saved into the cache,
     * @param int    $ttl   The ttl, in seconds, the value will live in the cache.
     *
     * @return void
     *
     * @throws Exception Thrown if an error occurred saving the token in cache.
     */
    public function saveAuthToken(string $token, int $ttl): void
    {
        if (!$this->_cache->set($this->_getAuthTokenKey(), $token, $ttl)) {
            throw new Exception('Error saving the azure active directory authentication token into redis cache.');
        }
    }

    /**
     * Helper function to generate the key under the azure active directory token is saved. The token represents the
     * Azure Active Directory Bearer token to authenticate the security principal.
     *
     * @return string
     */
    protected function _getAuthTokenKey(): string
    {
        return self::AZURE_AUTHENTICATOR_CACHE . $this->_getServiceTokenCacheKey();
    }

    /**
     * The Azure AD authentication for each service generates a Bearer token. That tokens should be saved into a cache
     * for reuse. This function differentiates the tokens keys by services.
     *
     * @return string
     */
    abstract protected function _getServiceTokenCacheKey(): string;
}