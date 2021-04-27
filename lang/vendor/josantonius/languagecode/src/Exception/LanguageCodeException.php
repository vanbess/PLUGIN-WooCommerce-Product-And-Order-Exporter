<?php declare(strict_types=1);
/**
 * List of 217 language codes: ISO 639-1.
 * 
 * @category   JST
 * @package    LanguageCode
 * @subpackage LanguageCodeException
 * @author     Josantonius - info@josantonius.com
 * @copyright  Copyright (c) 2017 JST PHP Framework
 * @license    https://opensource.org/licenses/MIT - The MIT License (MIT)
 * @version    1.0.0
 * @link       https://github.com/Josantonius/PHP-LanguageCode
 * @since      File available since 1.0.0 - Update: 2017-01-12
 */

namespace Josantonius\LanguageCode\Exception;

/**
 * Exception class for LanguageCode library.
 *
 * You can use an exception and error handler with this library.
 *
 * @since 1.0.0
 *
 * @link https://github.com/Josantonius/PHP-ErrorHandler
 */
class LanguageCodeException extends \Exception { 

    /**
     * Exception handler.
     *
     * @since 1.0.0
     *
     * @param string $msg    → message error (Optional)
     * @param int    $error  → error code (Optional)
     * @param int    $status → HTTP response status code (Optional)
     */
    public function __construct(string $msg = '', int $error = 0, int $status = 0) {

        $this->message    = $msg;
        $this->code       = $error;
        $this->statusCode = $status;
    }
}