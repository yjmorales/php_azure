<?php
/**
 * @author Yenier Jimenez <yjmorales86@gmail.com>
 */

namespace App\AzureServiceBus;

use DateTimeImmutable;
use DateTimeInterface;
use JsonSerializable;
use Ramsey\Uuid\Uuid;

/**
 * This abstraction holds the common data held by a message to be sent to Azure Service Bus.
 * It's encoded as a JSON when it's sent, so to support it this implements `JsonSerializable`
 */
abstract class AbstractMessageData implements JsonSerializable
{
    /**
     * Its intention is to represent the message sent by this web application.
     *
     * @var string
     */
    protected string $_uuid;

    /**
     * It represents the time this message is generated.
     *
     * @var DateTimeImmutable
     */
    protected DateTimeImmutable $_time;

    /**
     * Represents the event type name that originates this message data.
     *
     * @var string
     */
    protected string $_type;

    /**
     * @param string $type Represents the event type name that originates this message data.
     */
    public function __construct(string $type)
    {
        $this->_uuid = Uuid::uuid4();
        $this->_time = new DateTimeImmutable();
        $this->_type = $type;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->_type;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'uuid' => $this->_uuid,
            'time' => $this->_time->format(DateTimeInterface::RFC3339),
            'type' => $this->_type,
            'data' => $this->_dataAsArray(),
        ];
    }

    /**
     * Helper function to covert the specific event data into a array format for Json Serialization.
     *
     * @return array
     */
    abstract protected function _dataAsArray(): array;
}