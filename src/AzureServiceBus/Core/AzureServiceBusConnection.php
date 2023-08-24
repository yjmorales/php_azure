<?php
/**
 * @author Yenier Jimenez <yjmorales86@gmail.com>
 */

namespace App\AzureServiceBus\Core;

use App\AzureServiceBus\Core\Exception\AzureServiceBusException;
use DateInterval;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Response;

/**
 * This class represents a connection to Azure Service Bus API. It contains the operations to manage Azure Service Bus
 * resources.
 */
class AzureServiceBusConnection
{
    /**
     * Holds the Azure AD Token used to authorize the actions to perform.
     *
     * @var string
     */
    private string $_token;

    /**
     * Used to communicate with Azure Service Bus API.
     *
     * @var GuzzleClient
     */
    private GuzzleClient $_guzzleClient;

    /**
     * @param string $namespace Service Bus namespace used for access assignment.
     * @param string $token     Holds the Azure AD Token used to authorize the actions to perform.
     */
    public function __construct(string $namespace, string $token)
    {
        $this->_token        = $token;
        $this->_guzzleClient = new GuzzleClient(['base_uri' => "https://$namespace.servicebus.windows.net/"]);
    }

    /**
     * This function holds the logic to transport a message to the Azure Service Bus API. It sends the message to a
     * specific topic.
     *
     * @param AzureServiceBusMessage $message
     * @param string                 $topicName
     *
     * @return void
     * @throws AzureServiceBusException Thrown if the message was not able to be sent or there are other kind of errors.
     */
    public function sendToTopic(AzureServiceBusMessage $message, string $topicName): void
    {
        try {
            $response = $this->_guzzleClient->request('POST', "$topicName/messages", [
                    'headers' => [
                        'Content-Type'     => 'application/atom+xml;type=entry;charset=utf-8',
                        'Authorization'    => "Bearer $this->_token",
                        'BrokerProperties' => json_encode(['Label' => $message->getLabel()])
                    ],
                    "body"    => $message->getBody(),
                ]
            );
        } catch (GuzzleException $e) {
            throw new AzureServiceBusException('An error occurred communicating with the Azure Service Bus API. The message could not be sent to a topic.',
                0, $e);
        }

        $responseStatusCode = $response->getStatusCode();
        if (Response::HTTP_CREATED === $responseStatusCode) {
            return;
        }

        $this->_handleErrors($responseStatusCode);
    }

    /**
     * Function responsible to communicate with Azure Service Bus in order to get the messages count in the given queue.
     *
     * @param string $queueName Queue to be queried.
     *
     * @return AzureServiceBusQueueInfo
     *
     * @throws AzureServiceBusException
     */
    public function loadQueueInfo(string $queueName): AzureServiceBusQueueInfo
    {
        try {
            $response = $this->_guzzleClient->request('GET', "$queueName", [
                    'headers' => [
                        'Authorization' => "Bearer $this->_token",
                    ],
                ]
            );

            $bodyContent    = trim($response->getBody()->getContents());
            $bodyContentObj = simplexml_load_string($bodyContent);

        } catch (GuzzleException|Exception $e) {
            throw new AzureServiceBusException('An error occurred communicating with the Azure Service Bus API. The queue messages count cannot be retrieved.',
                0, $e);
        }

        if (!$bodyContentObj) {
            throw new AzureServiceBusException('The Azure Service Bus API returns an invalid queue information content.');
        }

        if (!$content = $bodyContentObj->content ?? null) {
            throw new AzureServiceBusException('The Azure Service Bus API returns an invalid queue information content.');
        }

        if (!$queueDescription = $content->QueueDescription ?? null) {
            throw new AzureServiceBusException('The Azure Service Bus API returns an invalid queue information description.');
        }

        try {
            $messageTtlPeriod = (string)$queueDescription->DefaultMessageTimeToLive;
            $messagesCount    = (int)$bodyContentObj->content->QueueDescription->MessageCount;

            return new AzureServiceBusQueueInfo($messagesCount, (new DateInterval($messageTtlPeriod))->d);
        } catch (Exception $e) {
            throw new AzureServiceBusException('An error retrieving the Query Information from the Azure Service Bus API response.');
        }
    }

    /**
     * Helper function used build the specific error based on the returned status code from Azure Service Bus.
     *
     * @param int $responseStatusCode Returned status code.
     *
     * @return void
     *
     * @throws AzureServiceBusException
     */
    private function _handleErrors(int $responseStatusCode): void
    {
        $exceptionalMessage = 'An error occurred communicating with the Azure Service Bus API.';
        switch ($responseStatusCode) {
            case 400:
                $exceptionalMessage .= ' Malformed payload.';
                break;
            case 401:
                $exceptionalMessage .= ' Authorization failed.';
                break;
            case 403:
                $exceptionalMessage .= ' Quota exceeded or message too large.';
                break;
            case 410:
                $exceptionalMessage .= ' Specified queue or topic does not exist.';
                break;
        }

        throw new AzureServiceBusException($exceptionalMessage);
    }
}