<?php

/**
 * Classe génératrice d'exceptions
 *
 * Permet la générations d'exceptions avec des messages génériques en fonction des arguments passés.
 * Permet également l'inclusion auto des informations de stack (classe, fonction, fichier, ligne, arguments) dans le message
 *
 * @method static instance_wrong_parameter()
 * @method static argument_wrong_type()
 * @method static argument_array_wrong_count()
 * @method static argument_array_missing_key()
 * @method static file_not_found()
 * @method static pdo_invalid_query()
 */
class ExceptionFactory {
    /**
     * Instance de la classe
     * Permet l'injection de mock pour les tests unitaires
     *
     * @var ExceptionFactory
     */
    protected static ?ExceptionFactory $instance;

    /**
     * Message de l'exception
     *
     * @var string
     */
    protected ?string $message;

    /**
     * Code de l'exception
     *
     * @var int
     */
    protected ?int $code;

    /**
     * Fichier de l'exception
     *
     * @var string
     */
    protected ?string $file;

    /**
     * Ligne de l'exception
     *
     * @var int
     */
    protected ?int $line;

    /**
     * Trace de l'exception
     *
     * @var string
     */
    protected ?string $trace;

    /**
     * Classe source de l'erreur
     *
     * @var string
     */
    protected ?string $class;

    /**
     * Exception précédente
     *
     * @var Throwable
     */
    protected ?Throwable $previous;

    /**
     * Stack de l'exception
     *
     * @var array
     */
    protected ?array $backtrace;

    /**
     * Fonction appelée lors de l'exception
     *
     * @var string
     */
    protected ?string $function;

    /**
     * Arguments de la fonction lors de l'exception
     *
     * @var array
     */
    protected ?array $arguments;

    /**
     * Permet de "reculer" dans le stack.
     * Utile lorsque le throw proviens d'une sous-fonction dont le call dépend d'une autre fonction
     *
     * @var int
     */
    protected ?int $backtraceOffset;

    const ADDITIONAL_OFFSET = 3;
    const INSTANCE_WRONG_PARAMETER = 1001;
    const ARGUMENT_WRONG_TYPE = 1002;
    const ARGUMENT_ARRAY_WRONG_COUNT = 1003;
    const INSTANCE_ARRAY_MISSING_KEY = 1004;
    const PDO_INVALID_QUERY = 1005;

    /**
     * Retourne l'instance de la classe
     *
     * @return ExceptionFactory
     */
    public static function getInstance(): ExceptionFactory{
        $instance = static::$instance ?? new static();
        $instance->reset();
        return $instance;
    }

    public function set(string $property, $value){
        $this->$property = $value;
    }

    /**
     * Réinitialise les propriétés de l'instance
     *
     * @return void
     */
    public function reset(){
        $properties = ['message', 'code', 'file', 'line', 'trace', 'class', 'previous', 'backtrace', 'function', 'arguments', 'backtraceOffset'];
        foreach($properties as $property){
            $this->set($property, null);
        }
    }

    /**
     * Récupère la stack
     *
     * @param int $offset
     * @return void
     */
    public function getStack(int $offset = 0){
        $offset = $offset + static::ADDITIONAL_OFFSET;

        $this->set('backtrace', debug_backtrace());
        $this->set('class', $this->backtrace[$offset]['class'] ?? '');
        $this->set('function', $this->backtrace[$offset]['function'] ?? '');
        $this->set('file', $this->backtrace[$offset]['file'] ?? '');
        $this->set('line', $this->backtrace[$offset]['line'] ?? '');
        $this->set('arguments', $this->backtrace[$offset]['args'] ?? '');
    }

    /**
     * Définit le header du message
     *
     * @return void
     */
    public function setHeader(){
        $message = '';
        $message .= 'In ' . $this->class;
        $message .='::' . $this->function;
        @$message .= '(' . implode(', ', $this->arguments) . '):' . PHP_EOL;
        $message .= 'Line ' . $this->line;
        $message .= ' in ' . $this->file . PHP_EOL;
        $message .= $this->message;

        $this->set('message', $message);
    }

    /**
     * Génère l'exception
     *
     * @param string $exceptionClass
     * @return Throwable
     */
    public function generate(string $exceptionClass): Throwable{
        $this->getStack($this->backtraceOffset ?? 0);
        $this->setHeader();
        return new $exceptionClass($this->message, $this->code, $this->previous);
    }

    /**
     * Generateur pour l'exception instance_wrong_parameter
     *
     * Utilisé lorsqu'un paramètre d'instance ou statique est incorrect dans un contexte de classe
     *
     * @param string $parameterName Nom du paramètre incorrect
     * @param mixed $parameterValue Valeur du paramètre incorrect
     * @param mixed $expectedValue Valeur attendue
     * @param string|null $hint Message d'erreur supplémentaire
     * @param Throwable|null $previous Exception précédente
     * @param int|null $backtraceOffset Offset de la stack
     * @return LogicException Exception générée
     */
    public static function instance_wrong_parameter(
        string $parameterName,
        $parameterValue,
        string $expectedValue,
        ?string $hint = null,
        ?Throwable $previous = null,
        ?int $backtraceOffset = null
    ): LogicException
    {
        
        $message = 'The parameter "'.$parameterName.'" has an invalid value.';
        @$message .='Expected: "'.$expectedValue.'", but received: "'.$parameterValue.'".';
        if ($hint) {
            $message .= PHP_EOL . $hint;
        }

        $exception = self::getInstance();
        $exception->set('message', $message);
        $exception->set('code', self::INSTANCE_WRONG_PARAMETER);
        $exception->set('previous', $previous);
        $exception->set('backtraceOffset', $backtraceOffset);

        return $exception->generate(LogicException::class);
    }

    /**
     * Generateur pour l'exception argument_wrong_type
     *
     * Utilisé lorsqu'un argument est d'un type innatendu
     *
     * @param string $argumentName Nom de l'argument incorrect
     * @param mixed $argumentValue Valeur de l'argument incorrect
     * @param mixed $expectedTypes Types attendus
     * @param string|null $hint Message d'erreur supplémentaire
     * @param Throwable|null $previous Exception précédente
     * @param int|null $backtraceOffset Offset de la stack
     * @return Throwable Exception générée
     */
    public static function argument_wrong_type(
        string $argumentName,
        $argumentValue,
        $expectedTypes,
        ?string $hint = null,
        ?Throwable $previous = null,
        ?int $backtraceOffset = null
    ): InvalidArgumentException
    {
        if(is_array($expectedTypes)){
            $expectedType = implode(', ', $expectedTypes);
        }

        $message = 'The argument "'.$argumentName.'" has an invalid type.';
        $message .='Expected: "'.$expectedType.'", but received: "'.gettype($argumentValue).'".';
        if ($hint) {
            $message .= PHP_EOL . $hint;
        }

        $exception = self::getInstance();
        $exception->set('message', $message);
        $exception->set('code', self::ARGUMENT_WRONG_TYPE);
        $exception->set('previous', $previous);
        $exception->set('backtraceOffset', $backtraceOffset);

        return $exception->generate(InvalidArgumentException::class);
    }

    /**
     * Generateur pour l'exception argument_array_missing_key
     *
     * Utilisé lorsqu'une clé est manquante dans un argument de type array
     *
     * @param string $argumentName Nom de l'argument incorrect
     * @param string $key Clé manquante
     * @param string|null $hint Message d'erreur supplémentaire
     * @param Throwable|null $previous Exception précédente
     * @param int|null $backtraceOffset Offset de la stack
     * @return Throwable Exception générée
     */
    public static function argument_array_missing_key(
        string $argumentName,
        string $key,
        ?string $hint = null,
        ?Throwable $previous = null,
        ?int $backtraceOffset = null
    ): InvalidArgumentException
    {
        $message = 'The argument "'.$argumentName.'" has a missing key.';
        $message .='Expected: "'.$key.'".';
        if ($hint) {
            $message .= PHP_EOL . $hint;
        }

        $exception = self::getInstance();
        $exception->set('message', $message);
        $exception->set('code', self::INSTANCE_ARRAY_MISSING_KEY);
        $exception->set('previous', $previous);
        $exception->set('backtraceOffset', $backtraceOffset);

        return $exception->generate(InvalidArgumentException::class);
    }

    /**
     * Generateur pour l'exception file_not_found
     *
     * Utilisé lorsqu'un fichier est introuvable
     *
     * @param string $path Chemin du fichier
     * @param string|null $hint Message d'erreur supplémentaire
     * @param Throwable|null $previous Exception précédente
     * @param int|null $backtraceOffset Offset de la stack
     * @return Exception Exception générée
     */
    public static function file_not_found(
        string $path,
        ?string $hint = null,
        ?Throwable $previous = null,
        ?int $backtraceOffset = null
    ): Exception
    {
        $message = 'The file "'.$path.'" was not found.';
        if ($hint) {
            $message .= PHP_EOL . $hint;
        }

        $exception = self::getInstance();
        $exception->set('message', $message);
        $exception->set('code', self::INSTANCE_ARRAY_MISSING_KEY);
        $exception->set('previous', $previous);
        $exception->set('backtraceOffset', $backtraceOffset);

        return $exception->generate(Exception::class);
    }

    /**
     * Generateur pour l'exception pdo_invalid_query
     *
     * Utilisé lorsqu'une requête PDO échoue
     * 
     * @param string $query Requête
     * @param array $parameters Paramètres
     * @param string|null $hint Message d'erreur supplémentaire
     * @param Throwable|null $previous Exception précédente
     * @param int|null $backtraceOffset Offset de la stack
     * @return Throwable Exception générée
     */
    public static function pdo_invalid_query(
        string $query,
        array $parameters,
        ?string $hint = null,
        ?Throwable $previous = null,
        ?int $backtraceOffset = null
    ): PDOException
    {
        $message = 'A PDO query failed with :' . PHP_EOL;
        $message .= 'Query: "'.$query.'".' . PHP_EOL;
        $message .= 'Parameters: "'.implode(', ', $parameters).'".' . PHP_EOL;

        if ($hint) {
            $message .= PHP_EOL . $hint;
        }

        $exception = self::getInstance();
        $exception->set('message', $message);
        $exception->set('code', self::PDO_INVALID_QUERY);
        $exception->set('previous', $previous);
        $exception->set('backtraceOffset', $backtraceOffset);

        return $exception->generate(PDOException::class);
    }

    /**
     * Generateur pour l'exception argument_array_wrong_count
     *
     * Utilisé lorsqu'un argument de type array a un nombre d'éléments incorrect
     *
     * @param string $argumentName Nom de l'argument incorrect
     * @param int $minCount Nombre minimum d'éléments
     * @param int $maxCount Nombre maximum d'éléments
     * @param int $actualCount Nombre d'éléments reçus
     * @param string|null $hint Message d'erreur supplémentaire
     * @param Throwable|null $previous Exception précédente
     * @param int|null $backtraceOffset Offset de la stack
     * @return Throwable Exception générée
     */
    public static function argument_array_wrong_count(
        string $argumentName,
        int $minCount,
        int $maxCount,
        int $actualCount,
        ?string $hint = null,
        ?Throwable $previous = null,
        ?int $backtraceOffset = null
    ): InvalidArgumentException
    {
        $message = 'The array "'.$argumentName.'" has an invalid count of elements.';
        $message .='Expected: "'.$minCount.'" to "'.$maxCount.'", but received: "'.$actualCount.'".';
        if ($hint) {
            $message .= PHP_EOL . $hint;
        }

        $exception = self::getInstance();
        $exception->set('message', $message);
        $exception->set('code', self::ARGUMENT_ARRAY_WRONG_COUNT);
        $exception->set('previous', $previous);
        $exception->set('backtraceOffset', $backtraceOffset);

        return $exception->generate(InvalidArgumentException::class);
    }

}
