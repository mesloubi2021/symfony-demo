<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Tests\Controller;

use AppBundle\Controller\BlogController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\Post;

/**
 * Functional test for the controllers defined inside BlogController.
 * See http://symfony.com/doc/current/book/testing.html#functional-tests
 *
 * Execute the application tests using this command (requires PHPUnit to be installed):
 *
 *     $ cd your-symfony-project/
 *     $ phpunit -c app
 *
 */
class BlogControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/blog/');

        $this->assertCount(
            Post::NUM_ITEMS,
            $crawler->filter('article.post'),
            'The homepage displays the right number of posts.'
        );
    }

    public function testAddZeroLength() {
        $blog = new BlogController();
        $result = $blog->add("");
        $this->assertEquals(0, $result);
    }

    public function testAddOneLength() {
        $blog = new BlogController();
        $result = $blog->add("3");
        $this->assertEquals(3, $result);
    }

    public function testAddTwoLength() {
        $blog = new BlogController();
        $result = $blog->add("1,9");
        $this->assertEquals(10, $result);
    }

    public function testAddUnknownLength() {
        $blog = new BlogController();

        $result = $blog->add("1,9,50,47,23,57,20"); // Do I have to build it randomly?
        $this->assertEquals(207, $result);
    }

    public function testAddLineSep() {
        $blog = new BlogController();

        $result = $blog->add("1\n9,50,47\n23,57,20");
        $this->assertEquals(207, $result);
    }

    public function testAddSep() {
        $blog = new BlogController();

        $result = $blog->add("//;\n1;2;5;8;6");
        $this->assertEquals(22, $result);
    }

    /**
     * @expectedException \Exception
     */
    public function testAddExceptionNeg() {
        try {
            $blog = new BlogController();
            $result = $blog->add("//;\n1;-1;5;-8;6");
        } catch (Exception $e) {
            $this->assertEquals($e->getMessage(), "negatives not allowed : -1, -8, ");
            return;
        }

        $this->fail();
    }

    public function testAddBigInt() {
        $blog = new BlogController();

        $result = $blog->add("//;\n1;2;5;8000;6");
        $this->assertEquals(14, $result);
    }

    public function testAddBigDelim() {
        $blog = new BlogController();

        $result = $blog->add("//***\n1***2***3");
        $this->assertEquals(6, $result);
    }
}
