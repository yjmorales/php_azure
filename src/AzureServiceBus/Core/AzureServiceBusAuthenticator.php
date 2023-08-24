<?php
/**
 * @author Yenier Jimenez <yjmorales86@gmail.com>
 */

namespace App\AzureServiceBus\Core;

use App\Core\Azure\Authentication\AbstractAzureAuthenticator;
use App\Core\Azure\Authentication\AbstractAzureAuthenticatorCache;
use App\Core\Azure\Authentication\Exception\AzureConnectionException;
use Psr\Log\LoggerInterface;

/**
 * This class is responsible to authenticate an Azure security principal into the Azure AD. The security principal
 * is the entity responsible to perform tasks on the Azure Service Bus.
 * Once a connection is established it can be used to perform all operations over an Azure service.
 * The authentication is done by using Azure Directory Service (AD). Further information refer to:
 *
 * @link: https://learn.microsoft.com/en-us/azure/service-bus-messaging/authenticate-application
 */
class AzureServiceBusAuthenticator extends AbstractAzureAuthenticator
{
    /**
     * When an Azure role is assigned to an Azure AD security principal, Azure grants access to those resources for
     * that security principal. Access can be scoped to the level of Service Bus namespace.
     *
     * This property is the namespace above referenced.
     *
     * @var string
     */
    private string $_namespace;

    /**
     * Holds the connection instance used to communicate with the Azure service.
     *
     * @var AzureServiceBusConnection
     */
    private AzureServiceBusConnection $_connection;

    /**
     * @inheritDoc
     *
     * @param string $namespace Service Bus namespace used for access assignment.
     */
    public function __construct(
        AbstractAzureAuthenticatorCache $cache,
        LoggerInterface $logger,
        string $clientId,
        string $tenantId,
        string $clientSecret,
        string $namespace
    ) {
        parent::__construct($cache, $logger, $clientId, $tenantId, $clientSecret);

        $this->_namespace = $namespace;
    }

    /**
     * Use this function to get an Azure connection. If the connection is already created then it is
     * returned, otherwise it's created and returned.
     *
     * @return AzureServiceBusConnection
     *
     * @throws AzureConnectionException
     */
    public function getConnection(): AzureServiceBusConnection
    {
        $this->_authenticate();

        return $this->_connection;
    }

    /**
     * @inheritDoc
     */
    protected function _getAuthenticationScopeUrl(): string
    {
        return 'https://servicebus.azure.net/.default';
    }

    /**
     * @inheritDoc
     */
    protected function _refreshConnection(string $token): void
    {
        $this->_connection = new AzureServiceBusConnection($this->_namespace, $token);
    }
}