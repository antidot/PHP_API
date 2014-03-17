<?php ob_start();
require_once 'AFS/SEARCH/afs_interval_helper.php';


class AfsIntervalHelperTest extends PHPUnit_Framework_TestCase
{
    public function testBuildIntervalWithBothBoundaries()
    {
        $interval = AfsIntervalHelper::create(10, 20);
        $this->assertEquals(10, $interval->get_lower_bound());
        $this->assertEquals(20, $interval->get_upper_bound());
    }
    public function testBuildIntervalWithLowerBound()
    {
        $interval = AfsIntervalHelper::create(10);
        $this->assertEquals(10, $interval->get_lower_bound());
        $this->assertEquals(null, $interval->get_upper_bound());
    }
    public function testBuildIntervalWithUpperBound()
    {
        $interval = AfsIntervalHelper::create(null, 20);
        $this->assertEquals(null, $interval->get_lower_bound());
        $this->assertEquals(20, $interval->get_upper_bound());
    }
    public function testBuildIntervalWithoutBound()
    {
        try {
            AfsIntervalHelper::create();
            $this->fail('Creating interval without bound should have raised error!');
        } catch (AfsIntervalBoundException $e) { }
    }

    public function testSerializeIntervalWithBothBoundaries()
    {
        $interval = AfsIntervalHelper::create(10, 20);
        $this->assertEquals('[10 .. 20]', (string)$interval);
    }
    public function testSerializeIntervalWithLowerBound()
    {
        $interval = AfsIntervalHelper::create(10);
        $this->assertEquals('[10 .. ' . PHP_INT_MAX . ']', (string)$interval);
    }
    public function testSerializeIntervalWithUpperhBound()
    {
        $interval = AfsIntervalHelper::create(null, 20);
        $this->assertEquals('[' . PHP_INT_MIN . ' .. 20]', (string)$interval);
    }

    public function testBuildIntervalFromStringValues()
    {
        $interval = AfsIntervalHelper::parse('[10.3 .. 20]');
        $this->assertEquals(10.3, $interval->get_lower_bound());
        $this->assertEquals(20, $interval->get_upper_bound());
    }
    public function testBuildIntervalFromStringValueAndMinInf()
    {
        $interval = AfsIntervalHelper::parse('[' . PHP_INT_MIN . ' .. 20]');
        $this->assertEquals(null, $interval->get_lower_bound());
        $this->assertEquals(20, $interval->get_upper_bound());
    }
    public function testBuildIntervalFromStringValueAndMaxInf()
    {
        $interval = AfsIntervalHelper::parse('[10 .. ' . PHP_INT_MAX . ']');
        $this->assertEquals(10, $interval->get_lower_bound());
        $this->assertEquals(null, $interval->get_upper_bound());
    }
    public function testBuildIntervalFromInvalidStringValue()
    {
        try {
            AfsIntervalHelper::parse('[10..20]');
            $this->fail('Invalid interval string should have not been parsed!');
        } catch (AfsIntervalInitializerException $e) { }
        try {
            AfsIntervalHelper::parse(']10 .. 20]');
            $this->fail('Invalid interval string should have not been parsed!');
        } catch (AfsIntervalInitializerException $e) { }
        try {
            AfsIntervalHelper::parse('[10 .. 20[');
            $this->fail('Invalid interval string should have not been parsed!');
        } catch (AfsIntervalInitializerException $e) { }
        try {
            AfsIntervalHelper::parse('[10 . 20]');
            $this->fail('Invalid interval string should have not been parsed!');
        } catch (AfsIntervalInitializerException $e) { }
    }
}
