<?php
/**
 * @author Yenier Jimenez <yjmorales86@gmail.com>
 */

namespace App\Core\Azure\Authentication\Exception;

use Exception;

/**
 * Exception to be thrown whenever an Azure connection cannot be established via Active Directory.
 */
class AzureConnectionException extends Exception
{
    // Intentionally blank.
}