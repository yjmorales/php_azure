<?php
/**
 * @author Yenier Jimenez <yjmorales86@gmail.com>
 */

namespace App\AzureBlobStorage\Service;

/**
 * Interface responsible to define the functions to retrieve images from Azure blob storage.
 */
interface IBase64ImgLoaderService
{
    /**
     * This function loads the base64 image.
     *
     * @return string
     */
    public function loadBase64Img(): string;
}