<?php
/**
 * @author Yenier Jimenez <yjmorales86@gmail.com>
 */

namespace App\AzureServiceBus;

/**
 * This class maps the data to be sent to Azure Service Bus as a message once an entity change.
 * It's encoded as a JSON when it's sent, so to support it this implements `JsonSerializable`
 */
class EntityChangedMessageData extends AbstractMessageData
{
    /**
     * @@inheritDoc
     *
     */
    public function __construct()
    {
        parent::__construct(EventTypes::ENTITY_CHANGED);
    }

    /**
     * @inheritDoc
     */
    protected function _dataAsArray(): array
    {
        /*
         * Todo: Define your own data to be send being part of this message.
         *
         *   return [
         *          'key' => value
         *          'key' => value
         *          'key' => value
         *   ];
         */
        return [];
    }
}