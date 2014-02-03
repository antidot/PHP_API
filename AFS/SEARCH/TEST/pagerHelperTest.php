<?php ob_start();
require_once 'AFS/SEARCH/afs_pager_helper.php';
require_once 'AFS/SEARCH/afs_query.php';
require_once 'AFS/SEARCH/afs_query_coder.php';

class PagerHelperTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultPage()
    {
        $input = json_decode('{
            "pager": {
                "nextPage": 2,
                "currentPage": 1,
                "page": [
                    1,
                    2
                ]
            }
        }');

        $helper = new AfsPagerHelper($input->pager, new AfsQuery(),
            new AfsHelperConfiguration());
        try {
            $helper->get_previous();
            $this->fail('No previous page available should have rosen exception!');
        } catch (OutOfBoundsException $e) { }
        $this->assertEquals($helper->get_next()->get_page(), 2);

        $pages = $helper->get_pages();
        $this->assertEquals(count($pages), 2);
        $this->assertTrue(array_key_exists(1, $pages));
        $this->assertEquals($pages[1]->get_page(), '1');
        $this->assertTrue(array_key_exists(2, $pages));
        $this->assertEquals($pages[2]->get_page(), '2');
        $this->assertEquals($helper->get_current_no(), 1);
    }

    public function testLastPage()
    {
        $input = json_decode('{
            "pager": {
                "previousPage": 2,
                "currentPage": 3,
                "page": [
                    1,
                    2,
                    3
                ]
            }
        }');

        $helper = new AfsPagerHelper($input->pager, new AfsQuery(),
            new AfsHelperConfiguration());
        try {
            $helper->get_next();
            $this->fail('No next page available should have rosen exception!');
        } catch (OutOfBoundsException $e) { }
        $this->assertEquals($helper->get_previous()->get_page(), 2);

        $pages = $helper->get_pages();
        $this->assertEquals(count($pages), 3);
        $this->assertTrue(array_key_exists(1, $pages));
        $this->assertEquals($pages[1]->get_page(), '1');
        $this->assertTrue(array_key_exists(2, $pages));
        $this->assertEquals($pages[2]->get_page(), '2');
        $this->assertTrue(array_key_exists(3, $pages));
        $this->assertEquals($pages[3]->get_page(), '3');
        $this->assertEquals($helper->get_current_no(), 3);
    }

    public function testFormatOnLastPage()
    {
        $input = json_decode('{
            "pager": {
                "previousPage": 2,
                "currentPage": 3,
                "page": [
                    1,
                    2,
                    3
                ]
            }
        }');

        $helper = new AfsPagerHelper($input->pager, new AfsQuery(),
            new AfsHelperConfiguration());
        $format = $helper->format();
        $this->assertFalse(array_key_exists('next', $format['pages']));
        $this->assertEquals($format['pages']['previous']->get_page(), 2);
        $this->assertEquals($format['pages'][1]->get_page(), '1');
        $this->assertEquals($format['pages'][2]->get_page(), '2');
        $this->assertEquals($format['pages'][3]->get_page(), '3');
        $this->assertEquals($format['current'], 3);
    }

    public function testSomePagesWithCoder()
    {
        $input = json_decode('{
            "pager": {
                "previousPage": 1,
                "nextPage": 3,
                "currentPage": 2,
                "page": [
                    1,
                    2,
                    3
                ]
            }
        }');

        $config = new AfsHelperConfiguration();
        $config->set_query_coder(new AfsQueryCoder('foo.php'));
        $helper = new AfsPagerHelper($input->pager, new AfsQuery(), $config);
        $this->assertEquals($helper->get_previous(), 'foo.php?replies=10');
        $this->assertEquals($helper->get_next(), 'foo.php?replies=10&page=3');

        $pages = $helper->get_pages();
        $this->assertEquals(count($pages), 3);
        $this->assertTrue(array_key_exists(1, $pages));
        $this->assertEquals($pages[1], 'foo.php?replies=10');
        $this->assertTrue(array_key_exists(2, $pages));
        $this->assertEquals($pages[2], 'foo.php?replies=10&page=2');
        $this->assertTrue(array_key_exists(3, $pages));
        $this->assertEquals($pages[3], 'foo.php?replies=10&page=3');
        $this->assertEquals($helper->get_current_no(), 2);
    }

    public function testFormatSomePagesWithCoder()
    {
        $input = json_decode('{
            "pager": {
                "previousPage": 1,
                "nextPage": 3,
                "currentPage": 2,
                "page": [
                    1,
                    2,
                    3
                ]
            }
        }');

        $config = new AfsHelperConfiguration();
        $config->set_query_coder(new AfsQueryCoder('foo.php'));
        $helper = new AfsPagerHelper($input->pager, new AfsQuery(), $config);
        $format = $helper->format();

        $this->assertEquals($format['pages']['previous'], 'foo.php?replies=10');
        $this->assertEquals($format['pages']['next'], 'foo.php?replies=10&page=3');
        $this->assertEquals($format['pages'][1], 'foo.php?replies=10');
        $this->assertEquals($format['pages'][2], 'foo.php?replies=10&page=2');
        $this->assertEquals($format['pages'][3], 'foo.php?replies=10&page=3');
        $this->assertEquals($format['current'], 2);
    }

    public function testRetrieveAllPagesWithoutPreviousAndNext()
    {
        $input = json_decode('{
            "pager": {
                "currentPage": 111,
                "page": [
                    1,
                    2
                ]
            }
        }');

        $config = new AfsHelperConfiguration();
        $config->set_query_coder(new AfsQueryCoder('foo.php'));
        $helper = new AfsPagerHelper($input->pager, new AfsQuery(), $config);
        $this->assertEquals(111, $helper->get_current_no());

        $pages = $helper->get_all_pages();
        $this->assertEquals(2, count($pages));
        $key_value = each($pages);
        $this->assertEquals('1', $key_value['key']);
        $this->assertEquals('foo.php?replies=10', $key_value['value']);
        $key_value = each($pages);
        $this->assertEquals('2', $key_value['key']);
        $this->assertEquals('foo.php?replies=10&page=2', $key_value['value']);
    }

    public function testRetrieveAllPagesWithPreviousWithoutNext()
    {
        $input = json_decode('{
            "pager": {
                "previousPage": 42,
                "currentPage": 111,
                "page": [
                    1,
                    2
                ]
            }
        }');

        $config = new AfsHelperConfiguration();
        $config->set_query_coder(new AfsQueryCoder('foo.php'));
        $helper = new AfsPagerHelper($input->pager, new AfsQuery(), $config);
        $this->assertEquals(111, $helper->get_current_no());

        $pages = $helper->get_all_pages();
        $this->assertEquals(3, count($pages));
        $key_value = each($pages);
        $this->assertEquals('previous', $key_value['key']);
        $this->assertEquals('foo.php?replies=10&page=42', $key_value['value']);
        $key_value = each($pages);
        $this->assertEquals('1', $key_value['key']);
        $this->assertEquals('foo.php?replies=10', $key_value['value']);
        $key_value = each($pages);
        $this->assertEquals('2', $key_value['key']);
        $this->assertEquals('foo.php?replies=10&page=2', $key_value['value']);
    }

    public function testRetrieveAllPagesWithoutPreviousWithNext()
    {
        $input = json_decode('{
            "pager": {
                "nextPage": 666,
                "currentPage": 111,
                "page": [
                    1,
                    2
                ]
            }
        }');

        $config = new AfsHelperConfiguration();
        $config->set_query_coder(new AfsQueryCoder('foo.php'));
        $helper = new AfsPagerHelper($input->pager, new AfsQuery(), $config);
        $this->assertEquals(111, $helper->get_current_no());

        $pages = $helper->get_all_pages();
        $this->assertEquals(3, count($pages));
        $key_value = each($pages);
        $this->assertEquals('1', $key_value['key']);
        $this->assertEquals('foo.php?replies=10', $key_value['value']);
        $key_value = each($pages);
        $this->assertEquals('2', $key_value['key']);
        $this->assertEquals('foo.php?replies=10&page=2', $key_value['value']);
        $key_value = each($pages);
        $this->assertEquals('next', $key_value['key']);
        $this->assertEquals('foo.php?replies=10&page=666', $key_value['value']);
    }

    public function testRetrieveAllPagesWithPreviousAndNext()
    {
        $input = json_decode('{
            "pager": {
                "previousPage": 42,
                "nextPage": 666,
                "currentPage": 111,
                "page": [
                    1,
                    2
                ]
            }
        }');

        $config = new AfsHelperConfiguration();
        $config->set_query_coder(new AfsQueryCoder('foo.php'));
        $helper = new AfsPagerHelper($input->pager, new AfsQuery(), $config);
        $this->assertEquals(111, $helper->get_current_no());

        $pages = $helper->get_all_pages();
        $this->assertEquals(4, count($pages));
        $key_value = each($pages);
        $this->assertEquals('previous', $key_value['key']);
        $this->assertEquals('foo.php?replies=10&page=42', $key_value['value']);
        $key_value = each($pages);
        $this->assertEquals('1', $key_value['key']);
        $this->assertEquals('foo.php?replies=10', $key_value['value']);
        $key_value = each($pages);
        $this->assertEquals('2', $key_value['key']);
        $this->assertEquals('foo.php?replies=10&page=2', $key_value['value']);
        $key_value = each($pages);
        $this->assertEquals('next', $key_value['key']);
        $this->assertEquals('foo.php?replies=10&page=666', $key_value['value']);
    }

}


