<?php

namespace Inertia\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Inertia\Controller\InertiaResponseTrait;
use Inertia\Utility\Message;

class UsersControllerTest extends TestCase
{
    use InertiaResponseTrait;
    use IntegrationTestTrait;

    public function testItReturnsInertiaWebViewResponseWithDefaultConvention()
    {
        $this->get('/users/index');

        $this->assertResponseOk();
        $this->assertContentType('text/html');
        $this->assertTemplate('app');
        $this->assertResponseContains('&quot;Users\/Index&quot');
        $this->assertResponseContains('&quot;http:\/\/localhost\/users\/index&quot');
        $this->assertResponseContains('&quot;name&quot;:&quot;InertiaTestApp&quot;');
        $this->assertResponseContains('props');
    }

    public function testItReturnsInertiaWebViewResponseWithCustomComponent()
    {
        $this->get('/users/custom-component');

        $this->assertResponseOk();
        $this->assertContentType('text/html');
        $this->assertTemplate('app');
        $this->assertResponseContains(htmlentities(json_encode('Custom/Component')));
    }

    public function testItReturnsInertiaJsonViewResponseWhenRequestIsXInertia()
    {
        $this->configRequest([
            'headers' => ['X-Inertia' => 'true'],
        ]);

        $this->get('/users/index');

        $this->assertResponseOk();
        $this->assertContentType('application/json');
    }

    public function testPartiaReloads()
    {
        $this->configRequest([
            'headers' => [
                'X-Inertia' => 'true',
                'X-Inertia-Partial-Data' => 'posts,postsCount',
                'X-Inertia-Partial-Component' => 'Users/Index',
            ],
        ]);

        $this->get('/users/index');

        $this->assertContentType('application/json');
        $this->assertResponseOk();

        $expected = json_encode([
            'component' => 'Users/Index',
            'url' => 'http://localhost/users/index',
            'props' => [
                'posts' => [
                    [
                        'title' => 'Title 1',
                        'body' => 'Body of title 1',
                    ],
                    [
                        'title' => 'Title 2',
                        'body' => 'Body of title 2',
                    ],
                ],
                'postsCount' => 2,
            ],
        ], JSON_PRETTY_PRINT);
        $this->assertEquals($expected, (string)$this->_response->getBody());
    }

    public function testItRedirectsWithSeeOtherResponseCode()
    {
        $this->configRequest([
            'headers' => ['X-Inertia' => 'true'],
        ]);

        $this->put('/users/store', ['test' => 'data']);

        $this->assertResponseCode(Message::STATUS_SEE_OTHER);
    }
}
