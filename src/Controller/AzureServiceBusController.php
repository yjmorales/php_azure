<?php
/**
 * @author Yenier Jimenez <yjmorales86@gmail.com>
 */

namespace App\Controller;

use App\AzureServiceBus\EntityEventManager;
use App\AzureServiceBus\EntityChangedMessageData;
use Exception;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/azure-service-bus")
 */
class AzureServiceBusController extends AbstractController
{
    /**
     * Renders the azure service bus page.
     *
     * @Route("/", name="azure_service_bus_index")
     */
    public function index(): Response
    {
        return $this->render('azure_service_bus/index.html.twig');
    }

    /**
     * Sends a `change` message to Azure Service Bus topic .
     *
     * @Route("/", name="azure_service_bus_index")
     *
     * @param EntityEventManager $manager
     * @param Logger             $logger
     *
     * @return JsonResponse
     */
    public function sendChangeMessageToTopic(EntityEventManager $manager, Logger $logger): JsonResponse
    {
        $success = true;
        try {
            $manager->notify(new EntityChangedMessageData(1));
        } catch (Exception $e) {
            $logger->error('An error occurred sending a message to the Azure Service Bus topic');
            $success = false;
        }

        return new JsonResponse([
            'success' => $success
        ]);
    }

    /**
     * This action retrieves the queue messages count from an azure service bus queue.
     *
     * @param EntityEventManager $manager
     * @param Logger             $logger
     *
     * @return JsonResponse
     */
    public function getQueueMessageCount(EntityEventManager $manager, Logger $logger): JsonResponse
    {
        $success = true;
        try {
            $manager->getQueueInfo(1);
        } catch (Exception $e) {
            $logger->error('An error occurred getting the message queue count from Azure Service Bus.');
            $success = false;
        }

        return new JsonResponse([
            'success' => $success
        ]);
    }
}
