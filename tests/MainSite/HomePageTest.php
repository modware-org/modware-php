<?php

namespace Tests\MainSite;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class HomePageTest extends TestCase
{
    private $client;
    private $baseUrl;

    protected function setUp(): void
    {
        parent::setUp();
        $this->baseUrl = getenv('APP_URL') . (getenv('APP_PORT') ? ':' . getenv('APP_PORT') : '');
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'http_errors' => false
        ]);
    }

    /**
     * Test main pages accessibility
     */
    public function testHomePageLoads()
    {
        $response = $this->client->get('/');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('text/html', $response->getHeaderLine('Content-Type'));
    }

    public function testAboutPageLoads()
    {
        $response = $this->client->get('/about');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('text/html', $response->getHeaderLine('Content-Type'));
    }

    public function testTrainingPageLoads()
    {
        $response = $this->client->get('/training');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('text/html', $response->getHeaderLine('Content-Type'));
    }

    public function testContactPageLoads()
    {
        $response = $this->client->get('/contact');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('text/html', $response->getHeaderLine('Content-Type'));
    }

    /**
     * Test critical elements presence
     */
    public function testHeaderExists()
    {
        $response = $this->client->get('/');
        $body = (string) $response->getBody();
        $this->assertStringContainsString('<header', $body);
        $this->assertStringContainsString('menu-section', $body);
    }

    public function testFooterExists()
    {
        $response = $this->client->get('/');
        $body = (string) $response->getBody();
        $this->assertStringContainsString('<footer', $body);
        $this->assertStringContainsString('footer-section', $body);
    }

    public function testAboutSectionExists()
    {
        $response = $this->client->get('/');
        $body = (string) $response->getBody();
        $this->assertStringContainsString('about-section', $body);
    }

    public function testProgramSectionExists()
    {
        $response = $this->client->get('/');
        $body = (string) $response->getBody();
        $this->assertStringContainsString('program-section', $body);
    }

    /**
     * Test error pages
     */
    public function test404PageLoads()
    {
        $response = $this->client->get('/non-existent-page');
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertStringContainsString('text/html', $response->getHeaderLine('Content-Type'));
        $body = (string) $response->getBody();
        $this->assertStringContainsString('404', $body);
    }

    public function test500PageExists()
    {
        $exists = file_exists(__DIR__ . '/../../500.php');
        $this->assertTrue($exists, '500 error page should exist');
    }

    /**
     * Test response headers
     */
    public function testSecurityHeaders()
    {
        $response = $this->client->get('/');
        
        // Check for common security headers
        $headers = $response->getHeaders();
        
        // X-Frame-Options prevents clickjacking
        $this->assertArrayHasKey('X-Frame-Options', $headers);
        
        // X-Content-Type-Options prevents MIME type sniffing
        $this->assertArrayHasKey('X-Content-Type-Options', $headers);
        
        // X-XSS-Protection enables browser's XSS filter
        $this->assertArrayHasKey('X-XSS-Protection', $headers);
    }

    /**
     * Test critical functionality
     */
    public function testNavigationLinks()
    {
        $response = $this->client->get('/');
        $body = (string) $response->getBody();
        
        // Check for important navigation links
        $this->assertStringContainsString('href="/about"', $body);
        $this->assertStringContainsString('href="/training"', $body);
        $this->assertStringContainsString('href="/contact"', $body);
    }

    public function testMetaTags()
    {
        $response = $this->client->get('/');
        $body = (string) $response->getBody();
        
        // Check for important meta tags
        $this->assertStringContainsString('<meta charset="', $body);
        $this->assertStringContainsString('<meta name="viewport"', $body);
        $this->assertStringContainsString('<meta name="description"', $body);
    }
}
