<?php
/**
 * @author Yenier Jimenez <yjmorales86@gmail.com>
 */

namespace App\AzureServiceBus\Core;

/**
 * Class responsible to map the Azure Service Bus queue information.
 */
class AzureServiceBusQueueInfo
{
    /**
     * Holds the active messages living in the queue.
     *
     * @var int
     */
    private int $_messageCount;

    /**
     * @var int Holds the message ttl defined by the queue. Once this value passes the messages are auto-deleted from
     *      the queue.
     */
    private int $_messageTtl;

    /**
     * @param int $messageCount Holds the active messages living in the queue.
     * @param int $messageTtl   Holds the message ttl defined by the queue
     */
    public function __construct(int $messageCount, int $messageTtl)
    {
        $this->_messageCount = $messageCount;
        $this->_messageTtl   = $messageTtl;
    }

    /**
     * Gets the count of active messages living in the queue.
     *
     * @return int
     */
    public function getMessageCount(): int
    {
        return $this->_messageCount;
    }

    /**
     * Holds the message ttl defined by the queue
     *
     * @return int
     */
    public function getMessageTtl(): int
    {
        return $this->_messageTtl;
    }
}