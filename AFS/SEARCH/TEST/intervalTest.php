<?php ob_start();
require_once 'AFS/SEARCH/afs_interval.php';


class AfsIntervalTest extends PHPUnit_Framework_TestCase
{
    public function testBuildIntervalWithBothBoundaries()
    {
        $interval = AfsInterval::create(10, 20);
        $this->assertEquals(10, $interval->get_lower_bound());
        $this->assertEquals(20, $interval->get_upper_bound());
        $this->assertFalse($interval->is_lower_bound_excluded());
        $this->assertFalse($interval->is_upper_bound_excluded());
    }
    public function testBuildIntervalWithLowerBound()
    {
        $interval = AfsInterval::create(10);
        $this->assertEquals(10, $interval->get_lower_bound());
        $this->assertEquals(null, $interval->get_upper_bound());
    }
    public function testBuildIntervalWithUpperBound()
    {
        $interval = AfsInterval::create(null, 20);
        $this->assertEquals(null, $interval->get_lower_bound());
        $this->assertEquals(20, $interval->get_upper_bound());
    }
    public function testBuildIntervalWithoutBound()
    {
        try {
            AfsInterval::create();
            $this->fail('Creating interval without bound should have raised error!');
        } catch (AfsIntervalBoundException $e) { }
    }

    public function testBuildIntervalExcludingLowerBound()
    {
        $interval = AfsInterval::create(10, 20)->exclude_lower_bound();
        $this->assertEquals(']10 .. 20]', (string)$interval);
        $this->assertTrue($interval->is_lower_bound_excluded());
        $this->assertFalse($interval->is_upper_bound_excluded());
    }
    public function testBuildIntervalExcludingUpperBound()
    {
        $interval = AfsInterval::create(10, 20)->exclude_upper_bound();
        $this->assertEquals('[10 .. 20[', (string)$interval);
        $this->assertFalse($interval->is_lower_bound_excluded());
        $this->assertTrue($interval->is_upper_bound_excluded());
    }
    public function testBuildIntervalExcludingBothBounds()
    {
        $interval = AfsInterval::create(10, 20)->exclude_upper_bound()
            ->exclude_lower_bound();
        $this->assertEquals(']10 .. 20[', (string)$interval);
        $this->assertTrue($interval->is_lower_bound_excluded());
        $this->assertTrue($interval->is_upper_bound_excluded());
    }

    public function testSerializeIntervalWithBothBoundaries()
    {
        $interval = AfsInterval::create(10, 20);
        $this->assertEquals('[10 .. 20]', (string)$interval);
    }
    public function testSerializeIntervalWithLowerBound()
    {
        $interval = AfsInterval::create(10);
        $this->assertEquals('[10 .. ' . PHP_INT_MAX . ']', (string)$interval);
    }
    public function testSerializeIntervalWithUpperhBound()
    {
        $interval = AfsInterval::create(null, 20);
        $this->assertEquals('[' . PHP_INT_MIN . ' .. 20]', (string)$interval);
    }

    public function testBuildIntervalFromStringValues()
    {
        $interval = AfsInterval::parse('[10.3 .. 20]');
        $this->assertEquals(10.3, $interval->get_lower_bound());
        $this->assertEquals(20, $interval->get_upper_bound());
        $this->assertFalse($interval->is_lower_bound_excluded());
        $this->assertFalse($interval->is_upper_bound_excluded());
    }
    public function testBuildIntervalFromStringValueAndMinInf()
    {
        $interval = AfsInterval::parse('[' . PHP_INT_MIN . ' .. 20]');
        $this->assertEquals(null, $interval->get_lower_bound());
        $this->assertEquals(20, $interval->get_upper_bound());
    }
    public function testBuildIntervalFromStringValueAndMaxInf()
    {
        $interval = AfsInterval::parse('[10 .. ' . PHP_INT_MAX . ']');
        $this->assertEquals(10, $interval->get_lower_bound());
        $this->assertEquals(null, $interval->get_upper_bound());
    }
    public function testBuildIntervalFromStringExcludingLowerBound()
    {
        $interval = AfsInterval::parse(']10 .. 20]');
        $this->assertEquals(10, $interval->get_lower_bound());
        $this->assertEquals(20, $interval->get_upper_bound());
        $this->assertTrue($interval->is_lower_bound_excluded());
        $this->assertFalse($interval->is_upper_bound_excluded());
    }
    public function testBuildIntervalFromStringExcludingUpperBound()
    {
        $interval = AfsInterval::parse('[10 .. 20[');
        $this->assertEquals(10, $interval->get_lower_bound());
        $this->assertEquals(20, $interval->get_upper_bound());
        $this->assertFalse($interval->is_lower_bound_excluded());
        $this->assertTrue($interval->is_upper_bound_excluded());
    }
    public function testBuildIntervalFromStringExcludingBothBounds()
    {
        $interval = AfsInterval::parse(']10 .. 20[');
        $this->assertEquals(10, $interval->get_lower_bound());
        $this->assertEquals(20, $interval->get_upper_bound());
        $this->assertTrue($interval->is_lower_bound_excluded());
        $this->assertTrue($interval->is_upper_bound_excluded());
    }
    public function testBuildIntervalFromInvalidStringValue()
    {
        try {
            AfsInterval::parse('[10..20]');
            $this->fail('Invalid interval string should have not been parsed!');
        } catch (AfsIntervalInitializerException $e) { }
        try {
            AfsInterval::parse('10 .. 20]');
            $this->fail('Invalid interval string should have not been parsed!');
        } catch (AfsIntervalInitializerException $e) { }
        try {
            AfsInterval::parse('[10 .. 20');
            $this->fail('Invalid interval string should have not been parsed!');
        } catch (AfsIntervalInitializerException $e) { }
        try {
            AfsInterval::parse('[10 . 20]');
            $this->fail('Invalid interval string should have not been parsed!');
        } catch (AfsIntervalInitializerException $e) { }
    }
}
