<?php
/**
 * @author Yenier Jimenez <yjmorales86@gmail.com>
 */

namespace App\AzureServiceBus\Core;

use App\Core\Azure\Authentication\AbstractAzureAuthenticatorCache;

/**
 * This class saves temporary Azure Service Bus authentication information to communicate with Azure AD.
 */
class AzureServiceBusAuthenticationCache extends AbstractAzureAuthenticatorCache
{
    /**
     * @inheritDoc
     */
    protected function _getServiceTokenCacheKey(): string
    {
        return 'azure-service-bus';
    }
}