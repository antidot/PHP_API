<?php

class FilterWrapper
{
    private $obj = null;

    public function __construct($obj)
    {
        $this->set_wrapped($obj);
    }

    public function set_wrapped($obj)
    {
        if (is_a($obj, 'FilterWrapper'))
            throw new Exception('Beurp');

        $this->obj = $obj;
        $this->obj->set_wrapper($this);
    }

    public function __get($name)
    {
        if (is_a($this->obj, 'FilterWrapper'))
            throw new Exception('Beurp');

        return $this->obj->__get($name);
    }

    public function __set($name, $params)
    {
        if (is_a($this->obj, 'FilterWrapper'))
            throw new Exception('Beurp');

        return $this->obj->__set($name, $params);
    }

    public function __call($name, $params)
    {
        if (is_a($this->obj, 'FilterWrapper'))
            throw new Exception('Beurp');

        // Bad thing for params here !!!!!
        return $this->obj->$name($params[0]);
    }

    public function to_string()
    {
        // TODO check obj
        return $this->obj->to_string();
    }
}

function filter($id)
{
    return new FilterWrapper(new Filter($id));
}

abstract class WrappedObject
{
    private $wrapper = null;

    public function set_wrapper(FilterWrapper $wrapper)
    {
        $this->wrapper = $wrapper;
    }

    public function get_wrapper()
    {
        return $this->wrapper;
    }
}


class Filter extends WrappedObject
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function __get($name)
    {
        $wrapper = $this->get_wrapper();
        $wrapper->set_wrapped(OperatorFactory::create($name, $this));
        return $wrapper;
    }

    public function to_string()
    {
        return $this->id;
    }
}


class OperatorFactory
{
    public static function create($name, Filter $filter)
    {
        if ('equal' == $name) {
            return new EqualOperator($filter);
        } elseif ('not_equal' == $name) {
            return new NotEqualOperator($filter);
        } elseif ('less' == $name) {
            return new LessOperator($filter);
        } elseif ('less_equal' == $name) {
            return new LessEqualOperator($filter);
        } elseif ('greater' == $name) {
            return new GreaterOperator($filter);
        } elseif ('greater_equal' == $name) {
            return new GreaterEqualOperator($filter);
        } else {
            throw Exception('Unknown operator: ' . $name);
        }
    }
}

abstract class OperatorBase extends WrappedObject
{
    private $filter = null;
    private $op_str = null;

    public function __construct(Filter $filter, $op_str)
    {
        $this->filter = $filter;
        $this->op_str = $op_str;
    }

    public function value($val)
    {
        $wrapper = $this->get_wrapper();
        $wrapper->set_wrapped(new ValuedFilter($this, $val));
        return $wrapper;
    }

    public function to_string()
    {
        return $this->filter->to_string() . $this->op_str;
    }
}

class EqualOperator extends OperatorBase
{
    public function __construct(Filter $filter)
    {
        parent::__construct($filter, '=');
    }
}

class NotEqualOperator extends OperatorBase
{
    public function __construct(Filter $filter)
    {
        parent::__construct($filter, '!=');
    }
}

class LessOperator extends OperatorBase
{
    public function __construct(Filter $filter)
    {
        parent::__construct($filter, '<');
    }
}

class LessEqualOperator extends OperatorBase
{
    public function __construct(Filter $filter)
    {
        parent::__construct($filter, '<=');
    }
}

class GreaterOperator extends OperatorBase
{
    public function __construct(Filter $filter)
    {
        parent::__construct($filter, '>');
    }
}

class GreaterEqualOperator extends OperatorBase
{
    public function __construct(Filter $filter)
    {
        parent::__construct($filter, '>=');
    }
}


class ValuedFilter extends WrappedObject
{
    private $op = null;
    private $value = null;

    public function __construct(OperatorBase $op, $value)
    {
        $this->op = $op;
        $this->value = $value;
    }

    public function to_string()
    {
        return $this->op->to_string() . $this->value;
    }

    public function __get($name)
    {
        $wrapper = $this->op->get_wrapper();
        $wrapper->set_wrapped(CombinatorFactory::create($name, $this));
        return $wrapper;
    }
}


class CombinedFilter extends WrappedObject
{
    private $comb = null;
    private $right = null;

    public function __construct(CombinatorBase $comb, $right)
    {
        $this->comb = $comb;
        $this->right = $right;
    }

    public function to_string()
    {
        return $this->comb->to_string() . $this->right->to_string();
    }

    public function __get($name)
    {
        return $this->right->__get($name);
    }

    public function __set($name, $value)
    {
        $wrapper = $this->get_wrapper();
        $wrapper->set_wrapped(CombinatorFactory::create($name, $this));
        return $wrapper;
    }
}


class CombinatorFactory
{
    public static function create($name, $left)
    {
        if ('and' == $name) {
            return new AndCombinator($left);
        } elseif ('or' == $name) {
            return new OrCombinator($left);
        } else {
            throw new Exception('Unknown combinator: ' . $name);
        }
    }
}

abstract class CombinatorBase extends WrappedObject
{
    private $left = null;
    private $right = null;
    private $comb = null;

    public function __construct($left, $comb)
    {
        $this->left = $left;
        $this->comb = $comb;
    }

    public function __call($name, $params)
    {
        $wrapper = $this->get_wrapper();
        if ('filter' == $name) {
            // Arrrrggggg
            $result = new Filter($params[0]);
        } elseif ('group' == $name) {
            throw new Exception('Not Implemented');
        } else {
            throw new Exception('Arrrrbg');
        }
        $wrapper->set_wrapped($result);
        return $wrapper;
    }

    public function to_string()
    {
        return $this->left->to_string() . ' ' . $this->comb . ' ';
    }
}

class AndCombinator extends CombinatorBase
{
    public function __construct($left)
    {
        parent::__construct($left, 'and');
    }
}

class OrCombinator extends CombinatorBase
{
    public function __construct($left)
    {
        parent::__construct($left, 'or');
    }
}


class FilterTest extends PHPUnit_Framework_TestCase
{
    public function testComparisons()
    {
        $this->assertEquals('ID=value', filter('ID')->equal->value('value')->to_string());
        $this->assertEquals('ID!=value', filter('ID')->not_equal->value('value')->to_string());
        $this->assertEquals('ID<value', filter('ID')->less->value('value')->to_string());
        $this->assertEquals('ID<=value', filter('ID')->less_equal->value('value')->to_string());
        $this->assertEquals('ID>value', filter('ID')->greater->value('value')->to_string());
        $this->assertEquals('ID>=value', filter('ID')->greater_equal->value('value')->to_string());
    }

    public function testCombination()
    {
        echo "FOO 7\n";
        filter('ID')->equal->value('value')->and;
        echo "FOO 8\n";
        $this->assertEquals('ID=value and ID=val', filter('ID')->equal->value('value')->and->filter('ID')->equal->value('val')->to_string());
        echo "FOO 9\n";
        $this->assertEquals('ID=value or ID=val', filter('ID')->equal->value('value')->and->filter('ID')->equal->value('val')->to_string());
        echo "FOO 10\n";
    }
}
