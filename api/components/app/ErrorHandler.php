<?php

namespace api\components\app;


use Yii;
use yii\base\Component;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\base\ExitException;
use yii\helpers\VarDumper;
use yii\web\HttpException;
use yii\web\Response;


class ErrorHandler extends Component
{
    /**
     * @var \Exception the exception that is being handled currently.
     */
    public $exception;
    
    public function register()
    {
        ini_set('display_errors', false);
        set_exception_handler([$this, 'handleException']);
        set_error_handler([$this, 'handleError']);
        register_shutdown_function([$this, 'handleFatalError']);
    }
    
    /**
     * Unregisters this error handler by restoring the PHP error and exception handlers.
     */
    public function unregister()
    {
        restore_error_handler();
        restore_exception_handler();
    }
    
    /**
     * Handles uncaught PHP exceptions.
     *
     * This method is implemented as a PHP exception handler.
     *
     * @param \Exception $exception the exception that is not caught
     */
    public function handleException($exception)
    {
        if ($exception instanceof ExitException) {
            return;
        }
        
        $this->exception = $exception;
        
        // disable error capturing to avoid recursive errors while handling exceptions
        $this->unregister();
        
        // set preventive HTTP status code to 500 in case error handling somehow fails and headers are sent
        http_response_code(500);
        
        try {
            $this->logException($exception);
            $this->clearOutput();
            $this->renderException($exception);
            if (!YII_ENV_TEST) {
                Yii::getLogger()->flush(true);
                exit(1);
            }
        } catch (\Exception $e) {
            // an other exception could be thrown while displaying the exception
            $msg = "An Error occurred while handling another error:\n";
            $msg .= (string) $e;
            $msg .= "\nPrevious exception:\n";
            $msg .= (string) $exception;
            if (YII_DEBUG) {
                echo '<pre>' . htmlspecialchars($msg, ENT_QUOTES, Yii::$app->charset) . '</pre>';
            } else {
                echo 'An internal server error occurred while handling error.';
            }
            $msg .= "\n\$_SERVER = " . VarDumper::export($_SERVER);
            error_log($msg);
            exit(1);
        }
        
        $this->exception = null;
    }
    
    
    /**
     * Handles PHP execution errors such as warnings and notices.
     *
     * This method is used as a PHP error handler. It will simply raise an [[ErrorException]].
     *
     * @param integer $code the level of the error raised.
     * @param string $message the error message.
     * @param string $file the filename that the error was raised in.
     * @param integer $line the line number the error was raised at.
     * @return boolean whether the normal error handler continues.
     *
     * @throws ErrorException
     */
    public function handleError($code, $message, $file, $line)
    {
        if (error_reporting() & $code) {
            // load ErrorException manually here because autoloading them will not work
            // when error occurs while autoloading a class
            if (!class_exists('yii\\base\\ErrorException', false)) {
                require_once(dirname(dirname(dirname(__DIR__))) . '/vendor/yiisoft/yii2/base/ErrorException.php');
            }
            $exception = new ErrorException($message, $code, $code, $file, $line);
            
            // in case error appeared in __toString method we can't throw any exception
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            array_shift($trace);
            foreach ($trace as $frame) {
                if ($frame['function'] === '__toString') {
                    $this->handleException($exception);
                    exit(1);
                }
            }
            
            throw $exception;
        }
        return false;
    }
    
    /**
     * Handles fatal PHP errors
     */
    public function handleFatalError()
    {
        // load ErrorException manually here because autoloading them will not work
        // when error occurs while autoloading a class
        if (!class_exists('yii\\base\\ErrorException', false)) {
            require_once(dirname(dirname(dirname(__DIR__))) . '/vendor/yiisoft/yii2/base/ErrorException.php');
        }
        
        $error = error_get_last();
        
        if (ErrorException::isFatalError($error)) {
            
            $exception = new ErrorException($error['message'], $error['type'], $error['type'], $error['file'], $error['line']);
            $this->exception = $exception;
            $this->logException($exception);
            $this->clearOutput();
            $this->renderException($exception);
            
            // need to explicitly flush logs because exit() next will terminate the app immediately
            Yii::getLogger()->flush(true);
            exit(1);
        }
    }
    
    /**
     * Logs the given exception
     * @param $exception
     */
    public function logException($exception)
    {
        $category = get_class($exception);
        if ($exception instanceof HttpException) {
            $category = 'yii\\web\\HttpException:' . $exception->statusCode;
        } elseif ($exception instanceof \ErrorException) {
            $category .= ':' . $exception->getSeverity();
        }
        Yii::error($exception, $category);
    }
    
    /**
     * Removes all output echoed before calling this method.
     */
    public function clearOutput()
    {
        // the following manual level counting is to deal with zlib.output_compression set to On
        for ($level = ob_get_level(); $level > 0; --$level) {
            if (!@ob_end_clean()) {
                ob_clean();
            }
        }
    }
    
    
    /**
     * Renders the exception.
     * @param \Exception $exception the exception to be rendered.
     */
    protected function renderException($exception)
    {
        if (Yii::$app->has('response')) {
            $response = Yii::$app->getResponse();
            // reset parameters of response to avoid interference with partially created response data
            // in case the error occurred while sending the response.
            $response->isSent = false;
            $response->stream = null;
            $response->data = null;
            $response->content = null;
            $response->format = Response::FORMAT_JSON;
        } else {
            $response = new Response();
        }
        
        // CORS
        $requestHeaders = Yii::$app->request->headers;
        if (isset($requestHeaders['Origin']) && !$response->headers['Access-Control-Allow-Origin']) {
            $response->headers->set('Access-Control-Allow-Origin', $requestHeaders['Origin']);
        }
        
        $response->data = $this->errorData($exception);
        
        if ($exception instanceof HttpException) {
            $response->setStatusCode($exception->statusCode);
        } else {
            $response->setStatusCode(500);
        }
        
        $response->send();
    }
    
    protected function errorData($exception)
    {
        if ($exception === null) {
            // action has been invoked not from error handler, but by direct route, so we display '404 Not Found'
            $exception = new HttpException(404, 'Page not found.');
        }
    
        if ($exception instanceof HttpException) {
            $code = $exception->statusCode;
        } else {
            $code = $exception->getCode();
        }
        if ($exception instanceof Exception) {
            $name = $exception->getName();
        } else {
            $name = 'Error';
        }
        if ($code) {
            $name .= " (#$code)";
        }
    
        if (YII_DEBUG) {
            $message = $exception->getMessage();
        } else {
            $message = 'An internal server error occurred.';
        }
    
        return [
            'name' => $name,
            'message' => $message,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $this->getExceptionTraceAsString($exception),/*preg_split('/#[\d]+ /', $exception->getTraceAsString()),*/
        ];
    }
    
    /**
     * @param $exception Exception
     * @return string
     */
    function getExceptionTraceAsString($exception) {
        $rtn = [];
        $count = 0;
        foreach ($exception->getTrace() as $frame) {
            $args = "";
            if (isset($frame['args'])) {
                $args = array();
                foreach ($frame['args'] as $arg) {
                    if (is_string($arg)) {
                        $args[] = "'" . $arg . "'";
                    } elseif (is_array($arg)) {
                        $args[] = "Array";
                    } elseif (is_null($arg)) {
                        $args[] = 'NULL';
                    } elseif (is_bool($arg)) {
                        $args[] = ($arg) ? "true" : "false";
                    } elseif (is_object($arg)) {
                        $args[] = get_class($arg);
                    } elseif (is_resource($arg)) {
                        $args[] = get_resource_type($arg);
                    } else {
                        $args[] = $arg;
                    }
                }
                $args = join(", ", $args);
            }
            $rtn[$count]  = sprintf( "#%s %s(%s): %s(%s)",
                $count,
                $frame['file'],
                $frame['line'],
                $frame['function'],
                $args );
            $count++;
        }
        return $rtn;
    }
}
