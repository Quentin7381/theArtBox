<?php

class ExceptionFactory {

    protected $message;
    protected $code;
    protected $file;
    protected $line;
    protected $trace;
    protected $class;
    protected $previous;
    protected $backtrace;
    protected $function;
    protected $arguments;
    protected $backtraceOffset;
    const ADDITIONAL_OFFSET = 2;

    const INSTANCE_WRONG_PARAMETER = 1001;
    const INSTANCE_WRONG_TYPE = 1002;
    const INSTANCE_WRONG_COUNT = 1003;
    const INSTANCE_ARRAY_MISSING_KEY = 1004;

    public function getStack($offset = 0){
        $offset = $offset + static::ADDITIONAL_OFFSET;

        $this->backtrace = debug_backtrace();
        $this->class = $this->backtrace[$offset]['class'];
        $this->function = $this->backtrace[$offset]['function'];
        $this->file = $this->backtrace[$offset]['file'];
        $this->line = $this->backtrace[$offset]['line'];
        $this->arguments = $this->backtrace[$offset]['args'];
    }

    public function setHeader(){
        $message = '';
        $message .= 'In ' . $this->class;
        $message .='::' . $this->function;
        $message .= '(' . implode(', ', $this->arguments) . ')' . PHP_EOL;
        $message .= ': Line ' . $this->line;
        $message .= ' in ' . $this->file . "\n";
        $message .= $this->message;

        $this->message = $message;
    }

    public function generate($exceptionClass){
        $this->getStack($this->backtraceOffset);
        $this->setHeader();
        return new $exceptionClass($this->message, $this->code, $this->previous);
    }

    public static function instance_wrong_parameter(
        $parameterName,
        $parameterValue,
        $expectedValue,
        $hint = null,
        $previous = null,
        $backtraceOffset = null
    ) {
        
        $message = 'The parameter "'.$parameterName.'" has an invalid value.';
        $message .='Expected: "'.$expectedValue.'", but received: "'.$parameterValue.'".';
        if ($hint) {
            $message .= PHP_EOL . $hint;
        }

        $exception = new ExceptionFactory();
        $exception->message = $message;
        $exception->code = self::INSTANCE_WRONG_PARAMETER;
        $exception->previous = $previous;
        $exception->backtraceOffset = $backtraceOffset;

        return $exception->generate(LogicException::class);
    }

    public static function argument_wrong_type(
        $argumentName,
        $argumentValue,
        $expectedTypes,
        $hint = null,
        $previous = null,
        $backtraceOffset = null
    )
    {
        if(is_array($expectedTypes)){
            $expectedType = implode(', ', $expectedTypes);
        }

        $message = 'The argument "'.$argumentName.'" has an invalid type.';
        $message .='Expected: "'.$expectedType.'", but received: "'.gettype($argumentValue).'".';
        if ($hint) {
            $message .= PHP_EOL . $hint;
        }

        $exception = new ExceptionFactory();
        $exception->message = $message;
        $exception->code = self::INSTANCE_WRONG_TYPE;
        $exception->previous = $previous;
        $exception->backtraceOffset = $backtraceOffset;

        return $exception->generate(InvalidArgumentException::class);
    }

    public static function argument_wrong_count(
        $argumentName,
        $minCount,
        $maxCount,
        $actualCount,
        $hint = null,
        $previous = null,
        $backtraceOffset = null
    )
    {
        $message = 'The argument "'.$argumentName.'" has an invalid count.';
        $message .='Expected: "'.$minCount.'" to "'.$maxCount.'", but received: "'.$actualCount.'".';
        if ($hint) {
            $message .= PHP_EOL . $hint;
        }

        $exception = new ExceptionFactory();
        $exception->message = $message;
        $exception->code = self::INSTANCE_WRONG_COUNT;
        $exception->previous = $previous;
        $exception->backtraceOffset = $backtraceOffset;

        return $exception->generate(InvalidArgumentException::class);
    }

    public static function argument_array_missing_key(
        $argumentName,
        $key,
        $hint = null,
        $previous = null,
        $backtraceOffset = null
    )
    {
        $message = 'The argument "'.$argumentName.'" is missing a key.';
        $message .='Expected: "'.$key.'".';
        if ($hint) {
            $message .= PHP_EOL . $hint;
        }

        $exception = new ExceptionFactory();
        $exception->message = $message;
        $exception->code = self::INSTANCE_ARRAY_MISSING_KEY;
        $exception->previous = $previous;
        $exception->backtraceOffset = $backtraceOffset;

        return $exception->generate(InvalidArgumentException::class);
    }

    public static function file_not_found(
        $path,
        $hint = null,
        $previous = null,
        $backtraceOffset = null
    )
    {
        $message = 'The file "'.$path.'" was not found.';
        if ($hint) {
            $message .= PHP_EOL . $hint;
        }

        $exception = new ExceptionFactory();
        $exception->message = $message;
        $exception->code = self::INSTANCE_ARRAY_MISSING_KEY;
        $exception->previous = $previous;
        $exception->backtraceOffset = $backtraceOffset;

        return $exception->generate(Exception::class);
    }

}
