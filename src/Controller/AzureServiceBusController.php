<?php
/**
 * @author Yenier Jimenez <yjmorales86@gmail.com>
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/azure-service-bus")
 */
class AzureServiceBusController extends AbstractController
{
    /**
     * @Route("/", name="azure_service_bus_index")
     */
    public function index(): Response
    {
        return $this->render('azure_service_bus/index.html.twig');
    }
}
