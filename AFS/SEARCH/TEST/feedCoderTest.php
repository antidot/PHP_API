<?php
require_once "AFS/SEARCH/afs_feed_coder.php";

class FeedCoderTest extends PHPUnit_Framework_TestCase
{
    public function testEncodeOneFeed()
    {
        $coder = new AfsFeedCoder();
        $feeds = array('feed');
        $this->assertTrue($coder->encode($feeds) == 'feed');
    }
    public function testEncodeOneFeedCollisionWithSeparator()
    {
        $coder = new AfsFeedCoder();
        $feeds = array('feed_feed');
        $this->assertTrue($coder->encode($feeds) == 'feed|_feed');
    }
    public function testEncodeOneFeedCollisionWithEscapeChar()
    {
        $coder = new AfsFeedCoder();
        $feeds = array('feed|feed');
        $this->assertTrue($coder->encode($feeds) == 'feed||feed');
    }
    public function testEncodeOneFeedCollisionWithRegexDelimiter()
    {
        $coder = new AfsFeedCoder();
        $feeds = array('feed~feed');
        $this->assertTrue($coder->encode($feeds) == 'feed~feed');
    }

    public function testEncodeMultipleFeeds()
    {
        $coder = new AfsFeedCoder();
        $feeds = array('feed', 'feed');
        $this->assertTrue($coder->encode($feeds) == 'feed_feed');
    }
    public function testEncodeMultipleFeedsCollisionWithSeparator()
    {
        $coder = new AfsFeedCoder();
        $feeds = array('feed_feed', 'food_food');
        $this->assertTrue($coder->encode($feeds) == 'feed|_feed_food|_food');
    }
    public function testEncodeMultipleFeedsCollisionWithEscapeChar()
    {
        $coder = new AfsFeedCoder();
        $feeds = array('feed|feed', 'food|food');
        $this->assertTrue($coder->encode($feeds) == 'feed||feed_food||food');
    }
    public function testEncodeMultipleFeedsCollisionWithRegexDelimiter()
    {
        $coder = new AfsFeedCoder();
        $feeds = array('feed~feed', 'food~food');
        $this->assertTrue($coder->encode($feeds) == 'feed~feed_food~food');
    }

    public function testDecodeOneFeed()
    {
        $coder = new AfsFeedCoder();
        $this->assertTrue(in_array('feed', $coder->decode('feed')));
    }
    public function testDecodeOneFeedCollisionWithSeparator()
    {
        $coder = new AfsFeedCoder();
        $this->assertTrue(in_array('feed_feed', $coder->decode('feed|_feed')));
    }
    public function testDecodeOneFeedCollisionWithEscapeChar()
    {
        $coder = new AfsFeedCoder();
        $this->assertTrue(in_array('feed|feed', $coder->decode('feed||feed')));
    }
    public function testDecodeOneFeedCollisionWithRegexDelimiter()
    {
        $coder = new AfsFeedCoder();
        $feeds = array('feed~feed');
        $this->assertTrue(in_array('feed~feed', $coder->decode('feed~feed')));
    }

    public function testDecodeMultipleFeeds()
    {
        $coder = new AfsFeedCoder();
        $feeds = $coder->decode('feed_food');
        $this->assertTrue(in_array('feed', $feeds));
        $this->assertTrue(in_array('food', $feeds));
    }
    public function testDecodeMultipleFeedsCollisionWithSeparator()
    {
        $coder = new AfsFeedCoder();
        $feeds = $coder->decode('feed|_feed_food|_food');
        $this->assertTrue(in_array('feed_feed', $feeds));
        $this->assertTrue(in_array('food_food', $feeds));
    }
    public function testDecodeMultipleFeedsCollisionWithEscapeChar()
    {
        $coder = new AfsFeedCoder();
        $feeds = $coder->decode('feed||feed_food||food');
        $this->assertTrue(in_array('feed|feed', $feeds));
        $this->assertTrue(in_array('food|food', $feeds));
    }
    public function testDecodeMultipleFeedsCollisionWithRegexDelimiter()
    {
        $coder = new AfsFeedCoder();
        $feeds = $coder->decode('feed~feed_food~food');
        $this->assertTrue(in_array('feed~feed', $feeds));
        $this->assertTrue(in_array('food~food', $feeds));
    }

    public function testEncodeDecode()
    {
        $coder = new AfsFeedCoder();
        $feeds = array('foo_foo', 'bar|_bar', '~|baz|baz~|~|_||__|||');
        $encode = $coder->encode($feeds);
        $decode = $coder->decode($encode);
        for ($i = 0; $i < count($feeds); $i++) {
            $this->assertTrue($feeds[$i] == $decode[$i]);
        }
    }

    public function testSpecificValueSeparator()
    {
        $coder = new AfsFeedCoder('o');
        $feeds = array('foo_foo', 'bar|_bar', '~|baz|baz~|~|_||__|||');
        $encode = $coder->encode($feeds);
        $this->assertTrue('f|o|o_f|o|oobar||_baro~||baz||baz~||~||_||||__||||||'
            == $encode);

        $decode = $coder->decode($encode);
        for ($i = 0; $i < count($feeds); $i++) {
            $this->assertTrue($feeds[$i] == $decode[$i]);
        }
    }

    public function testSpecificEscapeCharacter()
    {
        $coder = new AfsFeedCoder('_', 'a');
        $feeds = array('foo_foo', 'bar|_bar', '~|baz|baz~|~|_||__|||');
        $encode = $coder->encode($feeds);
        $this->assertTrue('fooa_foo_baar|a_baar_~|baaz|baaz~|~|a_||a_a_|||'
            == $encode);

        $decode = $coder->decode($encode);
        for ($i = 0; $i < count($feeds); $i++) {
            $this->assertTrue($feeds[$i] == $decode[$i]);
        }
    }
}

?>
