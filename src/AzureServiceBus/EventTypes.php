<?php
/**
 * @author Yenier Jimenez <yjmorales86@gmail.com>
 */

namespace App\AzureServiceBus;

/**
 * Holds the Event Type values set to be sent by the web application to Azure Service Bus.
 */
class EventTypes
{
    /**
     * Represents the event where an entity change.
     */
    const ENTITY_CHANGED = 'ENTITY_CHANGED';
}