<?php

/** @brief Base class for all filter expression construction errors. */
class AfsFilterException extends Exception
{ }

/** @brief Exception raised when invalid operator has been used. */
class AfsUnknownOperatorException extends AfsFilterException
{
    /** @brief Constructs new instance with appropriated error message.
     * @param $name [in] Name of the invalid requested operator.
     */
    public function __construct($name)
    {
        parent::__construct('Unknown filter operator: ' . $name);
    }
}

/** @brief Exception raised when invalid combination has been used. */
class AfsUnknownCombinatorException extends AfsFilterException
{
    /** @brief Constructs new instance with appropriated error message.
     * @param $name [in] Name of the invalid requested combination.
     */
    public function __construct($name)
    {
        parent::__construct('Unknown filter combinator: ' . $name);
    }
}
