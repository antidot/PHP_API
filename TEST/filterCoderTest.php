<?php
require_once "afs_filter_coder.php";

class FilterCoderTest extends PHPUnit_Framework_TestCase
{
    public function testEncodeOneFilterValue()
    {
        $coder = new AfsFilterCoder();
        $filters = array('filter' => array('value'));
        $this->assertTrue($coder->encode($filters) == 'filter_value');
    }
    public function testEncodeOneFilterValueCollisionWithValueSeparator()
    {
        $coder = new AfsFilterCoder();
        $filters = array('filter' => array('value_value'));
        $this->assertTrue($coder->encode($filters) == 'filter_value|_value');
    }
    public function testEncodeOneFilterValueCollisionWithEscapeChar()
    {
        $coder = new AfsFilterCoder();
        $filters = array('filter' => array('value|value'));
        $this->assertTrue($coder->encode($filters) == 'filter_value||value');
    }
    public function testEncodeOneFilterValueCollisionWithRegexDelimiter()
    {
        $coder = new AfsFilterCoder();
        $filters = array('filter' => array('value~value'));
        $this->assertTrue($coder->encode($filters) == 'filter_value~value');
    }

    public function testEncodeMultipleFilterValues()
    {
        $coder = new AfsFilterCoder();
        $filters = array('filter' => array('value', 'value'));
        $this->assertTrue($coder->encode($filters) == 'filter_value_value');
    }
    public function testEncodeMultipleFilterValuesCollisionWithValueSeparator()
    {
        $coder = new AfsFilterCoder();
        $filters = array('filter' => array('foo_foo', 'bar_bar'));
        $this->assertTrue($coder->encode($filters) == 'filter_foo|_foo_bar|_bar');
    }
    public function testEncodeMultipleFilterValuesCollisionWithEscapeChar()
    {
        $coder = new AfsFilterCoder();
        $filters = array('filter' => array('foo|foo', 'bar|bar'));
        $this->assertTrue($coder->encode($filters) == 'filter_foo||foo_bar||bar');
    }
    public function testEncodeMultipleFilterValuesCollisionWithRegexDelimiter()
    {
        $coder = new AfsFilterCoder();
        $filters = array('filter' => array('foo~foo', 'bar~bar'));
        $this->assertTrue($coder->encode($filters) == 'filter_foo~foo_bar~bar');
    }
    public function testEncodeMultipleFiltersMultipleValuesCollisionFilterSeparator()
    {
        $coder = new AfsFilterCoder();
        $filters = array('filter' => array('foo-foo', 'bar-bar'),
                         'filt' => array('fot-fot', 'baz-baz'));
        $this->assertTrue($coder->encode($filters) == 'filter_foo|-foo_bar|-bar-filt_fot|-fot_baz|-baz');
    }

    public function testDecodeOneFilterValue()
    {
        $coder = new AfsFilterCoder();
        $decoded = $coder->decode('filter_value');
        $this->assertArrayHasKey('filter', $decoded);
        $this->assertTrue(in_array('value', $decoded['filter']));
    }
    public function testDecodeOneFilterValueCollisionWithValueSeparator()
    {
        $coder = new AfsFilterCoder();
        $decoded = $coder->decode('filter_value|_value');
        $this->assertArrayHasKey('filter', $decoded);
        $this->assertTrue(in_array('value_value', $decoded['filter']));
    }
    public function testDecodeOneFilterValueCollisionWithEscapeChar()
    {
        $coder = new AfsFilterCoder();
        $decoded = $coder->decode('filter_value||value');
        $this->assertArrayHasKey('filter', $decoded);
        $this->assertTrue(in_array('value|value', $decoded['filter']));
    }
    public function testDecodeOneFilterValueCollisionWithRegexDelimiter()
    {
        $coder = new AfsFilterCoder();
        $decoded = $coder->decode('filter_value~value');
        $this->assertArrayHasKey('filter', $decoded);
        $this->assertTrue(in_array('value~value', $decoded['filter']));
    }

    public function testDecodeMultipleFilterValues()
    {
        $coder = new AfsFilterCoder();
        $decoded = $coder->decode('filter_value_val');
        $this->assertArrayHasKey('filter', $decoded);
        $this->assertTrue(in_array('value', $decoded['filter']));
        $this->assertTrue(in_array('val', $decoded['filter']));
    }
    public function testDecodeMultipleFilterValuesCollisionWithValueSeparator()
    {
        $coder = new AfsFilterCoder();
        $decoded = $coder->decode('filter_value|_value_val|_val');
        $this->assertArrayHasKey('filter', $decoded);
        $this->assertTrue(in_array('value_value', $decoded['filter']));
        $this->assertTrue(in_array('val_val', $decoded['filter']));
    }
    public function testDecodeMultipleFilterValuesCollisionWithEscapeChar()
    {
        $coder = new AfsFilterCoder();
        $decoded = $coder->decode('filter_value||value_val||val');
        $this->assertArrayHasKey('filter', $decoded);
        $this->assertTrue(in_array('value|value', $decoded['filter']));
        $this->assertTrue(in_array('val|val', $decoded['filter']));
    }
    public function testDecodeMultipleFilterValuesCollisionWithRegexDelimiter()
    {
        $coder = new AfsFilterCoder();
        $decoded = $coder->decode('filter_value~value_val~val');
        $this->assertArrayHasKey('filter', $decoded);
        $this->assertTrue(in_array('value~value', $decoded['filter']));
        $this->assertTrue(in_array('val~val', $decoded['filter']));
    }
    public function testDecodeMultipleFiltersMultipleValuesCollisionWithFilterSeparator()
    {
        $coder = new AfsFilterCoder();
        $decoded = $coder->decode('filter_value|-value_val|-val-filt_va|-va_v|-v');
        $this->assertArrayHasKey('filter', $decoded);
        $this->assertTrue(in_array('value-value', $decoded['filter']));
        $this->assertTrue(in_array('val-val', $decoded['filter']));
        $this->assertArrayHasKey('filt', $decoded);
        $this->assertTrue(in_array('va-va', $decoded['filt']));
        $this->assertTrue(in_array('v-v', $decoded['filt']));
    }

    public function testEncodeDecode()
    {
        $coder = new AfsFilterCoder();
        $filters = array('filter' => array('foo_foo', 'bar|_bar', '~|baz|baz~|~|_||__|||'),
            'filt' => array('bla~|||---_b___t'));
        $encode = $coder->encode($filters);
        $decode = $coder->decode($encode);
        foreach ($filters as $filter => $values) {
            $this->assertArrayHasKey($filter, $decode);
            foreach ($values as $value) {
                $this->assertTrue(in_array($value, $decode[$filter]));
            }
        }
    }

    public function testSpecificValueSeparator()
    {
        $coder = new AfsFilterCoder('r');
        $filters = array('filter' => array('foo_foo', 'bar|_bar', '~|baz|baz~|~|_||__|||'),
            'filt' => array('bla~|||---_b___t'));
        $encode = $coder->encode($filters);
        $this->assertEquals('filte|rrfoo_foorba|r||_ba|rr~||baz||baz~||~||_||||__||||||-filtrbla~|||||||-|-|-_b___t', $encode);

        $decode = $coder->decode($encode);
        foreach ($filters as $filter => $values) {
            $this->assertArrayHasKey($filter, $decode);
            foreach ($values as $value) {
                $this->assertTrue(in_array($value, $decode[$filter]));
            }
        }
    }

    public function testSpecificFilterSeparator()
    {
        $coder = new AfsFilterCoder('_', 'a');
        $filters = array('filter' => array('foo_foo', 'bar|_bar', '~|baz|baz~|~|_||__|||'),
            'filt' => array('bla~|||---_b___t'));
        $encode = $coder->encode($filters);
        $this->assertEquals('filter_foo|_foo_b|ar|||_b|ar_~||b|az||b|az~||~|||_|||||_|_||||||afilt_bl|a~||||||---|_b|_|_|_t', $encode);

        $decode = $coder->decode($encode);
        foreach ($filters as $filter => $values) {
            $this->assertArrayHasKey($filter, $decode);
            foreach ($values as $value) {
                $this->assertTrue(in_array($value, $decode[$filter]));
            }
        }
    }
}

?>

