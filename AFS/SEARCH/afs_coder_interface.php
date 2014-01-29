<?php

/** @brief Interface for encoding/decoding parameters.
 *
 * Encoding parameters results in a string which should be decoded in order to
 * retrieve original data.
 *
 * Example for filters:
 * @code
 * $filters = $query->get_parameters()['filter'];
 * $coder = new FilterCoder(); // implements AfsCoderInterface
 * $coded_filters = $coder->encode($filters);
 * $decoded_filters = $coder->decode($filters);
 * assert($filters == $decoded_filters);
 * @endcode
 */
interface AfsCoderInterface
{
    /** @brief Encode parameters.
     * @param $parameters [in] array of parameters.
     * @return encoded string.
     */
    public function encode(array $parameters);
    /** @brief Decode previously encoded parameters.
     * @param $parameters [in] encoded string representing parameters.
     * @return decoded parameters.
     */
    public function decode($parameters);
}


