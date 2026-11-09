<?php

namespace Tests\Feature;

use Tests\TestCase;

class SpanishSeoTest extends TestCase
{
    public function test_sitemap_includes_all_eight_spanish_landing_pages()
    {
        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

        $content = $response->getContent();

        $spanishPaths = [
            '/umrah-desde-espana',
            '/umrah-desde-barcelona',
            '/paquetes-umrah',
            '/agencia-viajes-barcelona',
            '/vuelos-internacionales',
            '/visados',
            '/hoteles-la-meca',
            '/umrah-2027',
        ];

        foreach ($spanishPaths as $path) {
            $this->assertStringContainsString($path, $content, "Sitemap should contain {$path}");
        }
    }

    public function test_spanish_landing_pages_render_server_side_seo_metadata()
    {
        $spanishPages = [
            '/umrah-desde-espana' => 'Umrah desde España',
            '/umrah-desde-barcelona' => 'Umrah desde Barcelona',
            '/paquetes-umrah' => 'Paquetes Umrah',
            '/agencia-viajes-barcelona' => 'Agencia de Viajes',
            '/vuelos-internacionales' => 'Vuelos Internacionales',
            '/visados' => 'Visados',
            '/hoteles-la-meca' => 'Hoteles en La Meca',
            '/umrah-2027' => 'Umrah 2027',
        ];

        foreach ($spanishPages as $path => $expectedTitleFragment) {
            $response = $this->get($path);
            $response->assertStatus(200);

            $content = $response->getContent();
            $this->assertStringContainsString($expectedTitleFragment, $content);
            $this->assertStringContainsString('canonical', $content);
            $this->assertStringContainsString('application/ld+json', $content);
        }
    }

    public function test_homepage_has_hreflang_and_travel_agency_schema()
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $content = $response->getContent();
        $this->assertStringContainsString('hreflang="es"', $content);
        $this->assertStringContainsString('hreflang="en"', $content);
        $this->assertStringContainsString('TravelAgency', $content);
        $this->assertStringContainsString('Badalona', $content);
    }

    public function test_inquiry_api_validates_and_accepts_submissions()
    {
        $response = $this->postJson('/api/inquiry', [
            'name' => 'Test Pilgrim',
            'phone' => '+34600112233',
            'email' => 'test@example.com',
            'subject' => 'Consulta Umrah desde España',
            'message' => 'Deseo información para viajar en familia en 2027',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }
}
