<?php
/**
 * @author Yenier Jimenez <yjmorales86@gmail.com>
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/azure-blob-storage")
 */
class AzureBlogStorageController extends AbstractController
{
    /**
     * @Route("/", name="azure_blob_storage_index")
     */
    public function index(): Response
    {
        return $this->render('azure_blob_storage/index.html.twig');
    }
}
