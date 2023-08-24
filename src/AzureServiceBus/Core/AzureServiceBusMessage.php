<?php
/**
 * @author Yenier Jimenez <yjmorales86@gmail.com>
 */

namespace App\AzureServiceBus\Core;

/**
 * This class represents the Azure Service Bus Message.
 */
class AzureServiceBusMessage
{
    /**
     * Contains the value of the `label` correlation filter. Once a message is sent, this value helps subscribers to
     * get the right message.
     *
     * @link https://learn.microsoft.com/en-us/azure/service-bus-messaging/topic-filters?WT.mc_id=Portal-Microsoft_Azure_ServiceBus#correlation-filters
     *
     * @var string
     */
    private string $_label;

    /**
     * Holds the body of the message.
     *
     * @var string|null
     */
    private ?string $_body = null;

    /**
     * @param string $label Contains the value of the `label` correlation filter. Once a message is sent, this value
     *                      helps subscribers to get the right message.
     */
    public function __construct(string $label)
    {
        $this->_label = $label;
    }

    /**
     * Sets the body of the message.
     *
     * @param string $body New body value.
     */
    public function setBody(string $body): void
    {
        $this->_body = $body;
    }

    /**
     * Returns the `label` correlation filter value.
     *
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->_label;
    }

    /**
     * Returns the body of the message.
     *
     * @return string|null
     */
    public function getBody(): ?string
    {
        return $this->_body;
    }
}