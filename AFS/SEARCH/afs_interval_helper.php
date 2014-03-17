<?php
require_once 'AFS/SEARCH/afs_interval_exception.php';


/** @brief Defines minimal value allowed for an integer. */
define('PHP_INT_MIN', ~PHP_INT_MAX);


/** @brief Helper to make intervals.
 */
class AfsIntervalHelper
{
    private $lower_bound = null;
    private $upper_bound = null;

    /** @brief Constructs new interval helper.
     *
     * Initialize new AfsIntervalHelper with at least one interval bound.
     *
     * @param $lower_bound [in] Lower bound of the interval (default: null).
     * @param $upper_bound [in] Upper bound of the interval (default: null).
     *
     * @exception AfsIntervalBoundException when both boundaries are null.
     */
    public function __construct($lower_bound=null, $upper_bound=null)
    {
        if (is_null($lower_bound) && is_null($upper_bound))
            throw new AfsIntervalBoundException('Boundaries cannot be null at the same time');
        if (PHP_INT_MIN == $lower_bound)
            $lower_bound = null;
        if (PHP_INT_MAX == $upper_bound)
            $upper_bound = null;
        $this->lower_bound = $lower_bound;
        $this->upper_bound = $upper_bound;
    }

    /** @brief Retrieves interval lower bound.
     * @return lower bound of the interval.
     */
    public function get_lower_bound()
    {
        return $this->lower_bound;
    }

    /** @brief Retrieves interval upper bound.
     * @return upper bound of the interval.
     */
    public function get_upper_bound()
    {
        return $this->upper_bound;
    }

    /** @brief Serialize this instance in string format.
     *
     * Returned value can be used to recreate new interval using
     * AfsIntervalHelper::parse method.
     *
     * @return string representation of the instance.
     */
    public function __toString()
    {
        return '[' . (is_null($this->lower_bound) ? PHP_INT_MIN : $this->lower_bound)
            . ' .. ' . (is_null($this->upper_bound) ? PHP_INT_MAX : $this->upper_bound) . ']';
    }

    /** @brief Creates new interval helper.
     *
     * Initialize new AfsIntervalHelper with at least one interval bound.
     *
     * @param $lower_bound [in] Lower bound of the interval (default: null).
     * @param $upper_bound [in] Upper bound of the interval (default: null).
     *
     * @exception AfsIntervalBoundException when both boundaries are null.
     */
    public static function create($lower_bound=null, $upper_bound=null)
    {
        return new AfsIntervalHelper($lower_bound, $upper_bound);
    }

    /** @brief Creates new interval helper from string.
     *
     * @param $value [in] String value to parse in order to create new
     *        AfsIntervalHelper instance.
     *
     * @return newly created instance.
     */
    public static function parse($value)
    {
        $bound_pattern = '([0-9."-]+)';
        $interval_pattern = '/^\[' . $bound_pattern . ' .. ' . $bound_pattern . '\]$/';
        $matches = array();
        $result = preg_match($interval_pattern, $value, $matches);
        if (1 == $result) {
            if (count($matches) != 3)
                throw new Exception('Invalid number of matching elements, please contact Antidot support team');
            return new AfsIntervalHelper($matches[1], $matches[2]);
        } elseif (0 == $result) {
            throw new AfsIntervalInitializerException($value);
        } else {
            throw new Exception('Please contact Antidot support for this PHP API!');
        }
    }
}
