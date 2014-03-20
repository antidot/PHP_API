<?php
require_once 'AFS/SEARCH/afs_interval_exception.php';


/** @brief Defines minimal value allowed for an integer. */
define('PHP_INT_MIN', ~PHP_INT_MAX);


/** @brief Helper to make intervals.
 */
class AfsInterval
{
    private $lower_bound = null;
    private $lower_bound_excluded = false;
    private $upper_bound = null;
    private $upper_bound_excluded = false;

    /** @brief Constructs new interval helper.
     *
     * Initialize new AfsInterval with at least one interval bound.
     *
     * @param $lower_bound [in] Lower bound of the interval.
     * @param $upper_bound [in] Upper bound of the interval (default: null).
     *
     * @exception AfsIntervalBoundException when both boundaries are null.
     */
    public function __construct($lower_bound, $upper_bound=null)
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
    /** @brief Excludes lower bound from the interval.
     *
     * By default, both boundaries are included.
     */
    public function exclude_lower_bound()
    {
        $this->lower_bound_excluded = true;
        return $this;
    }
    /** @brief Checks wether lower bound is excluded or not.
     * @return @c True when lower bound is excluded from the interval, @c false
     *         otherwise.
     */
    public function is_lower_bound_excluded()
    {
        return $this->lower_bound_excluded;
    }

    /** @brief Retrieves interval upper bound.
     * @return upper bound of the interval.
     */
    public function get_upper_bound()
    {
        return $this->upper_bound;
    }
    /** @brief Excludes upper bound from the interval.
     *
     * By default, both boundaries are included.
     */
    public function exclude_upper_bound()
    {
        $this->upper_bound_excluded = true;
        return $this;
    }
    /** @brief Checks wether upper bound is excluded or not.
     * @return @c True when upper bound is excluded from the interval, @c false
     *         otherwise.
     */
    public function is_upper_bound_excluded()
    {
        return $this->upper_bound_excluded;
    }


    /** @brief Serialize this instance in string format.
     *
     * Returned value can be used to recreate new interval using
     * AfsInterval::parse method.
     *
     * @return string representation of the instance.
     */
    public function __toString()
    {
        return $this->get_left_interval_sign()
            . (is_null($this->lower_bound) ? PHP_INT_MIN : $this->lower_bound)
            . ' .. '
            . (is_null($this->upper_bound) ? PHP_INT_MAX : $this->upper_bound)
            . $this->get_right_interval_sign();
    }

    private function get_left_interval_sign()
    {
        if ($this->lower_bound_excluded)
            return ']';
        else
            return '[';
    }
    private function get_right_interval_sign()
    {
        if ($this->upper_bound_excluded)
            return '[';
        else
            return ']';
    }

    /** @brief Creates new interval helper.
     *
     * Initialize new AfsInterval with at least one interval bound.
     *
     * @param $lower_bound [in] Lower bound of the interval (default: null).
     * @param $upper_bound [in] Upper bound of the interval (default: null).
     *
     * @exception AfsIntervalBoundException when both boundaries are null.
     */
    public static function create($lower_bound=null, $upper_bound=null)
    {
        return new AfsInterval($lower_bound, $upper_bound);
    }

    /** @brief Creates new interval helper from string.
     *
     * @param $value [in] String value to parse in order to create new
     *        AfsInterval instance.
     *
     * @return newly created instance.
     */
    public static function parse($value)
    {
        $bound_pattern = '([0-9."-]+)';
        $sign_pattern = '(\[|\])';
        $interval_pattern = '/^' . $sign_pattern . $bound_pattern . ' .. ' . $bound_pattern . $sign_pattern . '$/';
        $matches = array();
        $result = preg_match($interval_pattern, $value, $matches);
        if (1 == $result) {
            if (count($matches) != 5)
                throw new Exception('Invalid number of matching elements, please contact Antidot support team');
            $interval = new AfsInterval($matches[2], $matches[3]);
            if (']' == $matches[1])
                $interval->exclude_lower_bound();
            if ('[' == $matches[4])
                $interval->exclude_upper_bound();
            return $interval;
        } elseif (0 == $result) {
            throw new AfsIntervalInitializerException($value);
        } else {
            throw new Exception('Please contact Antidot support for this PHP API!');
        }
    }
}
