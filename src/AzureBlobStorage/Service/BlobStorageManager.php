<?php
/**
 * @author Yenier Jimenez <yjmorales86@gmail.com>
 */

namespace App\AzureBlobStorage\Service;

use App\AzureBlobStorage\Exception\AzureBlobStorageConnectionException;
use App\AzureBlobStorage\Exception\AzureBlobStorageException;
use App\AzureBlobStorage\Exception\AzureBlobStorageNotFoundException;
use Exception;
use Symfony\Component\HttpKernel\KernelInterface;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\BlobRestProxy as BlobStorageConnection;
use Psr\Log\LoggerInterface;

/**
 * Class responsible to manage the resources of the Azure Blob Storage account.
 */
class BlobStorageManager
{
    /**
     * The containers used to save the image in Azure Blob Storage service are prefixed by the
     * following string. The reason is to identify via name the reason of the container within the Blob Storage Account.
     */
    private const CONTAINER_PREFIX = 'prefix_here';

    /**
     * Holds the authenticator to open a connection with the Azure Blob Storage service.
     *
     * @var BlobStorageAuthenticator
     */
    private BlobStorageAuthenticator $_bsAuthenticator;

    /**
     * Symfony kernel interface used to build the temporary directory where to download the images.
     *
     * @var KernelInterface
     */
    private KernelInterface $_kernel;

    /**
     * Holds the Azure BZLob container name used to save the base64 img.
     *
     * @var string
     */
    private string $_containerName;

    /**
     * Responsible to log failure operations over Blob Storage service.
     *
     * @var LoggerInterface
     */
    private LoggerInterface $_logger;

    /**
     * Holds the connection with the Azure Blob Storage service.
     *
     * @var BlobStorageConnection
     */
    private $_bsConnection;

    /**
     * BlobStorageManager constructor.
     *
     * @param BlobStorageAuthenticator $bsAuthenticator Holds the authenticator to open a connection with the Azure
     *                                                  Blob Storage service.
     * @param string                   $containerName   Holds the Azure Blob container name used to save the img.
     * @param KernelInterface          $kernel          Symfony kernel interface used to build the temporary directory
     *                                                  where to download the images.
     * @param LoggerInterface          $logger          Responsible to log failure operations over Blob Storage service.
     */
    public function __construct(
        BlobStorageAuthenticator $bsAuthenticator,
        string $containerName,
        KernelInterface $kernel,
        LoggerInterface $logger
    ) {
        $this->_bsAuthenticator = $bsAuthenticator;
        $this->_containerName   = $containerName;
        $this->_kernel          = $kernel;
        $this->_logger          = $logger;
    }

    /**
     * Use this method to obtain the image base64 value from azure.
     *
     * @return string
     * @throws AzureBlobStorageNotFoundException
     * @throws AzureBlobStorageException
     * @throws AzureBlobStorageConnectionException
     */
    public function getBase64Image(): string
    {
        /*
         * The images are downloaded from Blob Storage Service and are witten into a temporary
         * directory for rendering purposes.
         */
        try {
            // Starting by creating a connection to Azure Blob Storage service.
            $this->connect();

            // Verifying the img existence, which is the same as blob existence within azure blob storage.
            // All the blobs are prefixed by the transaction id. By that value will be filtered the blob list.
            $options = new ListBlobsOptions();
            $options->setPrefix($this->_buildBlobPrefix());
            $blobs = $this->_bsConnection->listBlobs($this->_containerName, $options)->getBlobs();

            // Getting the blob content.
            $blobName     = (head($blobs))->getName();
            $blobResource = $this->_bsConnection->getBlob($this->_containerName, $blobName)->getContentStream();

            // Temporary storing the blob content for rendering purposes.
            $tmpDir            = $this->_initTmpDir($blobName);
            $sanitizedBlobName = str_replace('/', '-', $blobName);
            $fileName          = str_replace(':', '-', "$tmpDir/$sanitizedBlobName");
            file_put_contents($fileName, $blobResource);

            // returns the respective image base64 value for rendering purposes.
            $base64 = base64_encode(file_get_contents($fileName));
            unlink($fileName);

            return $base64;
        } catch (AzureBlobStorageConnectionException|AzureBlobStorageNotFoundException $e) {
            throw $e;
        } catch (Exception $e) {
            $this->_logger->error("An error occurred retrieving the image base64." . $e->getMessage());
            throw new AzureBlobStorageException("Unable to obtain the image base64.", 0, $e);
        }
    }

    /**
     * Function to create a blob inside the specified container.
     *
     * @param string $imageBase64 Holds the content of the blob to be created.
     *
     * @return void
     * @throws AzureBlobStorageConnectionException
     * @throws AzureBlobStorageException
     */
    public function upload(string $imageBase64): void
    {
        try {
            $this->connect();
            $imgExtension = $this->_getImageFileType($imageBase64);
            $blobName     = $this->_buildBlobName($imgExtension);
            $this->_bsConnection->createBlockBlob($this->_containerName, $blobName, base64_decode($imageBase64));
        } catch (AzureBlobStorageConnectionException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new AzureBlobStorageException("Unable to upload to Blob Storage Service the image.", 0, $e);
        }
    }

    /**
     * Helper function to establish the connection with the Azure Blob Storage service. Always establish a connection.
     *
     * @return void
     * @throws AzureBlobStorageConnectionException
     */
    private function connect(): void
    {
        $this->_bsConnection = $this->_bsAuthenticator->getBsConnection();
    }

    /**
     * Helper function to build and return the blob name
     *
     * @param string $imageExtension The image extension used to be part of the blob name.
     *
     * @return string
     */
    private function _buildBlobName(string $imageExtension): string
    {
        return "{$this->_kernel->getEnvironment()}/" . self::CONTAINER_PREFIX . "-.$imageExtension";
    }

    /**
     * Helper function to build the prefix used to name a blob. This prefix also defines the folder where the blob
     * will be saved.
     *
     * @return string
     */
    private function _buildBlobPrefix(): string
    {
        return "{$this->_kernel->getEnvironment()}";
    }

    /**
     * Helper function to obtain the temporary directory where to save the img for rendering.
     *
     * @param string $blobName Name of the blob used to build the temporary directory.
     *
     * @return string
     *
     * @throws Exception
     */
    private function _initTmpDir(string $blobName): string
    {
        if (empty($blobName)) {
            throw new Exception('The client subdomain and blob name should not be empty strings.');
        }
        $blobNameSections = explode('/', $blobName);
        $env              = $blobNameSections[0] ?? false;
        if (!$env) {
            throw new Exception('Unable to initialize the directory for temporary img rendering.');
        }

        $cacheFolderName = 'azure_service_bus';
        $tmpDir          = "{$this->_kernel->getCacheDir()}/$cacheFolderName/$env";

        if (!(file_exists($tmpDir))) {
            mkdir($tmpDir, 0770, true);
        }

        return $tmpDir;
    }

    /**
     * Helper function to determinate the file extension respective to the given base64 image
     *
     * @param string $imageBase64 Image base64 value use to determinate the file extension.
     *
     * @return string
     * @throws Exception
     */
    private function _getImageFileType(string $imageBase64): string
    {
        $fileInfo  = finfo_open();
        $extension = finfo_buffer($fileInfo, base64_decode($imageBase64), FILEINFO_EXTENSION);
        if (!$extension) {
            throw new Exception('Unable to determinate the file extension respective to the given base64 image.');
        }
        $extension = explode('/', $extension);

        return $extension[0];
    }
}