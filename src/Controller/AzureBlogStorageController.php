<?php
/**
 * @author Yenier Jimenez <yjmorales86@gmail.com>
 */

namespace App\Controller;

use App\AzureBlobStorage\Service\BlobStorageManager;
use Exception;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/azure-blob-storage")
 */
class AzureBlogStorageController extends AbstractController
{
    /**
     * This action renders the  azure blob storage page.
     *
     * @Route("/", name="azure_blob_storage_index")
     */
    public function index(): Response
    {
        return $this->render('azure_blob_storage/index.html.twig');
    }

    /**
     * This action send a base64 image to azure blob stage.
     *
     * @Route("/upload", name="azure_blob_storage_upload")
     */
    public function upload(BlobStorageManager $blobStorageManager, Logger $logger): Response
    {
        $success = true;
        try {
            $base64 = 'Base64ValueHere';
            $blobStorageManager->upload($base64);
        } catch (Exception $e) {
            $success = false;
            $logger->error("Unable to upload base64 value to azure. {$e->getMessage()}");
        }

        return new JsonResponse(['success' => $success]);
    }

    /**
     * This action retrieves a base64 image from azure blob stage.
     *
     * @Route("/pull", name="azure_blob_storage_pull")
     */
    public function pull(BlobStorageManager $blobStorageManager, Logger $logger): Response
    {
        $success = true;
        try {
            $blobStorageManager->getBase64Image();
        } catch (Exception $e) {
            $success = false;
            $logger->error("Unable to pull out the base64 value to azure. {$e->getMessage()}");
        }

        return new JsonResponse(['success' => $success]);
    }
}
