<?php
/**
 * @author Yenier Jimenez <yjmorales86@gmail.com>
 */

namespace App\AzureServiceBus;

use App\AzureServiceBus\Core\AzureServiceBusAuthenticator;
use App\AzureServiceBus\Core\AzureServiceBusConnection;
use App\AzureServiceBus\Core\AzureServiceBusMessage;
use App\AzureServiceBus\Core\AzureServiceBusQueueInfo;
use App\AzureServiceBus\Core\Exception\AzureServiceBusException;
use App\Core\Azure\Authentication\Exception\AzureConnectionException;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * This class holds the functions to send messages to Azure Service Bus (ASB).
 * Note: The initials ASB represents the name Azure Service Bus.
 */
class EntityEventManager
{
    /**
     * Holds the authenticator to open a connection to ASB.
     *
     * @var AzureServiceBusAuthenticator
     */
    private AzureServiceBusAuthenticator $_azureAuthenticator;

    /**
     * Logger instance used to log exceptional behaviours.
     *
     * @var LoggerInterface
     */
    private LoggerInterface $_logger;

    /**
     * Established connection used to communicate the ASB().
     *
     * @var AzureServiceBusConnection
     */
    private AzureServiceBusConnection $_asbConnection;

    /**
     * This is the ASB topic name to send messages to. It's already defined by Azure administrator. It's NOT
     * dynamically created.
     *
     * @var string
     */
    private string $_topicName;

    /**
     * This is the ASB queue prefix name.
     *
     * @var string
     */
    private string $_queueName;

    /**
     * @param AzureServiceBusAuthenticator $azureAuthenticator Holds the authenticator to open a connection to ASB.
     * @param LoggerInterface              $logger             Logger instance used to log exceptional behaviours.
     * @param string                       $topicName          This is the ASB topic name to send messages to.
     * @param string                       $queueName          This is the ASB queue name.
     */
    public function __construct(
        AzureServiceBusAuthenticator $azureAuthenticator,
        LoggerInterface $logger,
        string $topicName,
        string $queueName
    ) {
        $this->_azureAuthenticator = $azureAuthenticator;
        $this->_logger             = $logger;
        $this->_topicName          = $topicName;
        $this->_queueName          = $queueName;
    }

    /**
     * This function sends the message to the Azure Service Bus topic in response an event.
     *
     * @param AbstractMessageData $data Holds the message respective to an event. +It's used to generate the message to
     *                                  be sent.
     *
     * @return void
     *
     * @throws AzureConnectionException Thrown if the authentication cannot be done.
     * @throws AzureServiceBusException Thrown if any error occurred sending the message.
     */

    public function notify(AbstractMessageData $data)
    {
        $messageType = $data->getType();
        try {
            $this->_connect();
            $message = new AzureServiceBusMessage($messageType);
            $message->setBody(json_encode($data));
            $this->_asbConnection->sendToTopic($message, $this->_topicName);
        } catch (AzureConnectionException $e) {
            throw $e;
        } catch (Exception $e) {
            $this->_logger->error("An error occurred sending a $messageType message type to Azure Service Bus topic. {$e->getMessage()}");
            throw new AzureServiceBusException("An error occurred sending a $messageType message to Azure Service Bus topic.",
                0, $e);
        }
    }

    /**
     * This function returns the number messages present in a queue.
     *
     * @return AzureServiceBusQueueInfo
     *
     * @throws AzureConnectionException
     * @throws AzureServiceBusException
     */
    public function getQueueInfo(): AzureServiceBusQueueInfo
    {
        try {
            $this->_connect();

            return $this->_asbConnection->loadQueueInfo($this->_queueName);
        } catch (AzureConnectionException $e) {
            throw $e;
        } catch (Exception $e) {
            $this->_logger->error("An error occurred retrieving the number queue messages. {$e->getMessage()}");
            throw new AzureServiceBusException("An error occurred retrieving the number queue messages.", 0, $e);
        }
    }

    /**
     * Helper function to establish the connection with Azure.
     *
     * @return void
     *
     * @throws AzureConnectionException
     */
    private function _connect(): void
    {
        $this->_asbConnection = $this->_azureAuthenticator->getConnection();
    }
}