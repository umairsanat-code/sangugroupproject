<?php

namespace App\Services;

use App\Models\Blog;
use App\Models\SeoMetadata;
use App\Models\UmrahPackage;
use App\Models\Visa;

/**
 * Resolves per-route SEO metadata for the server-rendered SPA shell.
 *
 * The public front-end is a Vue SPA served from a single Blade view
 * (resources/views/app.blade.php). That view only receives the data we pass
 * here, so this service is responsible for producing a correct, absolute,
 * canonical-aware <head> for every public route — even for routes whose body
 * is rendered client-side.
 *
 * Precedence:
 *   1. An admin-managed row in `seo_metadata` (exact path, then legacy alias).
 *   2. A value derived from the database (blog / visa / umrah).
 *   3. A sensible built-in default for a known static path.
 */
class SeoResolver
{
    /** Public business identity used for structured data (verified from the site footer). */
    public const BRAND = 'Sangu Travel & Tours';
    public const BRAND_ALT = 'SanguGroup';
    public const PHONE = '+923085092631';
    public const EMAIL = 'sangutravel@gmail.com';

    /**
     * Static public pages. Keyed by canonical path.
     * `robots` defaults to "index, follow"; `noindex` pages are forms/transient.
     */
    private const STATIC_PAGES = [
        '/'          => ['Sangu Travel & Tours | Flights, Visa, Umrah & Hotel Booking', 'Book flights, hotels, Umrah packages and visa services worldwide with Sangu Travel & Tours. Trusted, personal travel support.', 'index, follow', 'website'],
        '/about'     => ['About Sangu Travel & Tours | Trusted Travel Agency', 'Learn about Sangu Travel & Tours — a travel agency offering flights, visa assistance, Umrah packages and hotel bookings worldwide.', 'index, follow', 'website'],
        '/services'  => ['Our Travel Services | Sangu Travel & Tours', 'Flights, visa services, Umrah packages, hotels and custom itineraries — explore the full range of Sangu Travel & Tours services.', 'index, follow', 'website'],
        '/team'      => ['Our Team | Sangu Travel & Tours', 'Meet the travel experts behind Sangu Travel & Tours, dedicated to arranging your flights, visas, Umrah and hotel bookings.', 'index, follow', 'website'],
        '/flights'   => ['Flight Booking Worldwide | Sangu Travel & Tours', 'Search and book flights with airlines worldwide through Sangu Travel & Tours.', 'index, follow', 'website'],
        '/book'      => ['Book Your Trip | Sangu Travel & Tours', 'Complete your booking with Sangu Travel & Tours.', 'noindex, follow', 'website'],
        '/visa'      => ['Visa Services | Sangu Travel & Tours', 'Apply for tourist and visit visas with document guidance and fast processing through Sangu Travel & Tours.', 'index, follow', 'website'],
        '/umrah'     => ['Umrah Packages | Sangu Travel & Tours', 'Curated Umrah packages with flights, hotels near the Haramain and guided support from Sangu Travel & Tours.', 'index, follow', 'website'],
        '/hotels'    => ['Hotels & Accommodation Worldwide | Sangu Travel & Tours', 'Find and book hotels and accommodation worldwide with Sangu Travel & Tours.', 'index, follow', 'website'],
        '/gallery'   => ['Gallery | Sangu Travel & Tours', 'Photos of journeys arranged by Sangu Travel & Tours.', 'index, follow', 'website'],
        '/esim'      => ['Travel eSIM | Sangu Travel & Tours', 'Stay connected abroad with travel eSIM data plans from Sangu Travel & Tours.', 'index, follow', 'website'],
        '/blog'      => ['Travel Blog | Sangu Travel & Tours', 'Guides, Umrah tips and travel advice from the Sangu Travel & Tours blog.', 'index, follow', 'website'],
        '/contact'   => ['Contact Sangu Travel & Tours', 'Get in touch with Sangu Travel & Tours for flights, visas, Umrah packages and hotel bookings.', 'index, follow', 'website'],
        '/terms'     => ['Terms & Conditions | Sangu Travel & Tours', 'Terms and conditions for services provided by Sangu Travel & Tours.', 'index, follow', 'website'],
        // Dedicated Spanish SEO landing pages
        '/umrah-desde-espana' => [
            'Umrah desde España 2026 - 2027 | Paquetes con Vuelos y Visado - SanguGroup',
            'Reserve su paquete de Umrah desde España con SanguGroup. Salidas desde Barcelona y Madrid, hoteles 5★ frente al Haram, visado incluido y asistencia en español y urdu.',
            'index, follow',
            'website'
        ],
        '/umrah-desde-barcelona' => [
            'Umrah desde Barcelona 2026 | Salidas desde Aeropuerto El Prat - SanguGroup',
            'Viaje a la Umrah desde Barcelona con SanguGroup. Salidas desde El Prat (BCN), hoteles cerca de la Kaaba, visado y asesoría presencial en nuestra agencia de Badalona.',
            'index, follow',
            'website'
        ],
        '/paquetes-umrah' => [
            'Paquetes Umrah 2026 - 2027 | Precios Económicos, Familiares y 5★ - SanguGroup',
            'Descubra el catálogo completo de paquetes de Umrah desde España. Opciones económicas, familiares y 5★ Clock Tower frente a Masjid Al-Haram con vuelos y visado.',
            'index, follow',
            'website'
        ],
        '/agencia-viajes-barcelona' => [
            'Agencia de Viajes en Barcelona y Badalona | Vuelos, Umrah y Visados - SanguGroup',
            'Fly Planet Badalona (SanguGroup), su agencia de viajes en Barcelona. Billetes de avión a Pakistán, Dubái y todo el mundo, paquetes de Umrah y tramitación de visados.',
            'index, follow',
            'website'
        ],
        '/vuelos-internacionales' => [
            'Vuelos Internacionales Baratos desde España | Billetes a Pakistán y Dubái - SanguGroup',
            'Ofertas en billetes de avión desde Barcelona y Madrid a Pakistán (Lahore, Islamabad, Karachi), Dubái, Arabia Saudí y Reino Unido con equipaje ampliado hasta 46 kg.',
            'index, follow',
            'website'
        ],
        '/visados' => [
            'Gestión de Visados Internacionales en España | Arabia Saudí, EAU, UK y Schengen',
            'Tramitación profesional de visados turísticos y de visita para Arabia Saudí, Dubái, Reino Unido y países Schengen. Asesoría rápida en nuestra oficina de Barcelona.',
            'index, follow',
            'website'
        ],
        '/hoteles-la-meca' => [
            'Hoteles en La Meca y Medina | Reserva Frente al Haram y Clock Tower - SanguGroup',
            'Reserve hoteles 3★, 4★ y 5★ en La Meca y Medina. Alojamientos en el complejo Abraj Al-Bait (Torre del Reloj) y frente a la Mezquita del Profeta al mejor precio garantizado.',
            'index, follow',
            'website'
        ],
        '/umrah-2027' => [
            'Umrah 2027 desde España | Reserva Anticipada con Descuento - SanguGroup',
            'Asegure su plaza para la temporada de Umrah 2027 desde España. Tarifas reducidas por reserva anticipada, plazas para Ramadán 2027 y facilidades de pago en cuotas.',
            'index, follow',
            'website'
        ],
    ];

    /**
     * Legacy admin paths that never matched a real front-end route.
     * Maps the real path => the legacy path an admin may have stored.
     */
    private const PATH_ALIASES = [
        '/about'  => '/about-us',
        '/hotels' => '/hotel',
    ];

    public function resolve(string $path): object
    {
        $path = $this->normalizePath($path);

        $base = $this->canonicalBase();
        $canonical = $base . ($path === '/' ? '/' : $path);

        $data = [
            'path' => $path,
            'title' => null,
            'description' => null,
            'h1' => null,
            'canonical_url' => $canonical,
            'robots' => 'index, follow',
            'og_image' => null,
            'og_type' => 'website',
            'schema_markup' => null,
        ];

        // 1. Admin-managed metadata (exact path, then legacy alias).
        $row = $this->safe(static fn () => SeoMetadata::where('path', $path)->first());
        if (! $row && isset(self::PATH_ALIASES[$path])) {
            $row = $this->safe(static fn () => SeoMetadata::where('path', self::PATH_ALIASES[$path])->first());
        }
        if ($row) {
            $data['title'] = $this->clean($row->title) ?: null;
            $data['description'] = $this->clean($row->description) ?: null;
            $data['h1'] = $this->clean($row->h1) ?: null;
            if ($row->canonical_url && $this->isSafeCanonical((string) $row->canonical_url)) {
                $data['canonical_url'] = $row->canonical_url;
            }
            $data['schema_markup'] = $this->normalizeSchema($row->schema_markup);
        }

        // 2. Database-derived metadata for dynamic routes.
        $this->applyDynamic($path, $base, $data);

        // 2b. Dedicated Spanish landing pages
        $this->applySpanishLanding($path, $base, $data);

        // 3. Static defaults (only fill gaps left by admin/DB).
        if (isset(self::STATIC_PAGES[$path])) {
            [$title, $description, $robots, $type] = self::STATIC_PAGES[$path];
            $data['title'] ??= $title;
            $data['description'] ??= $description;
            $data['robots'] = $robots;
            $data['og_type'] = $type;
        }

        // 4. Private / transient areas must never be indexed.
        if ($this->isPrivate($path)) {
            $data['robots'] = 'noindex, nofollow';
        }

        // Final canonical guard: never point at a www or localhost host.
        $data['canonical_url'] = $this->finalizeCanonical((string) $data['canonical_url'], $base);

        if (empty($data['title'])) {
            $data['title'] = self::BRAND . ' | Flights, Visa, Umrah & Hotel Booking';
        }
        if (empty($data['description'])) {
            $data['description'] = 'Flights, hotels, Umrah packages and visa services worldwide — arranged by ' . self::BRAND . '.';
        }
        if (empty($data['og_image'])) {
            $data['og_image'] = $base . '/images/logo.png';
        }

        $data['site_url'] = $base . '/';
        $data['brand'] = self::BRAND;
        $data['brand_alt'] = self::BRAND_ALT;
        $data['phone'] = self::PHONE;
        $data['email'] = self::EMAIL;

        return (object) $data;
    }

    private function applyDynamic(string $path, string $base, array &$data): void
    {
        // /blog/{slug}
        if (preg_match('#^/blog/(.+)$#', $path, $m)) {
            $blog = $this->safe(static fn () => Blog::where('slug', $m[1])->first());
            if ($blog) {
                $data['title'] ??= $blog->title . ' | ' . self::BRAND . ' Blog';
                $data['description'] ??= $this->clean($blog->excerpt) ?: $this->excerpt((string) $blog->content);
                $data['h1'] ??= $blog->title;
                $data['og_type'] = 'article';
                $data['og_image'] = $this->absoluteImage($blog->image_url, $base);
                if (! $data['schema_markup']) {
                    $data['schema_markup'] = $this->blogPostingSchema($blog, $base);
                }
            }

            return;
        }

        // /visa/{slug}
        if (preg_match('#^/visa/([^/]+)$#', $path, $m)) {
            $visa = $this->safe(static fn () => Visa::where('slug', $m[1])->first());
            if ($visa) {
                $name = $this->clean($visa->country ?? null) ?: $this->humanizeSlug($m[1]);
                $data['title'] ??= ($this->clean($visa->title ?? null) ?: $name . ' Visa Services') . ' | ' . self::BRAND;
                $data['description'] ??= 'Apply for your ' . $name . ' visa with document guidance and fast processing from ' . self::BRAND . '.';
                $data['h1'] ??= $name . ' Visa';
                $data['og_image'] = $this->absoluteImage($visa->image_url ?? null, $base) ?? $data['og_image'];
            }

            return;
        }

        // /umrah/{slug}
        if (preg_match('#^/umrah/(.+)$#', $path, $m)) {
            $package = $this->safe(static fn () => UmrahPackage::where('slug', $m[1])->first());
            if ($package && ! empty($package->title)) {
                $data['title'] ??= $package->title . ' | ' . self::BRAND;
                $data['description'] ??= 'Book the ' . $package->title . ' with ' . self::BRAND . ' — flights, hotels near the Haramain and guided support.';
                $data['h1'] ??= $package->title;
            }

            return;
        }
    }

    private function applySpanishLanding(string $path, string $base, array &$data): void
    {
        $spanishMeta = [
            '/umrah-desde-espana' => [
                'h1' => 'Paquetes de Umrah desde España 2026 - 2027 | Vuelos, Hoteles y Visado',
                'name' => 'Umrah desde España',
                'faqs' => [
                    ['q' => '¿Qué incluye exactamente el paquete de Umrah desde España?', 'a' => 'Nuestros paquetes completos incluyen: billetes de avión ida y vuelta desde Barcelona o Madrid, tramitación oficial del visado saudí con seguro médico, alojamiento en hoteles seleccionados en La Meca y Medina, traslados en autobús climatizado y asistencia continua.'],
                    ['q' => '¿Puedo pagar mi paquete a plazos desde España?', 'a' => 'Sí. En SanguGroup ofrecemos facilidades de pago flexible con depósito inicial y cuotas mensuales antes del viaje mediante transferencia o en nuestra oficina de Badalona.'],
                    ['q' => '¿Es obligatorio que las mujeres viajen con Mahram?', 'a' => 'De acuerdo con las normativas actualizadas del Ministerio de Hajj y Umrah de Arabia Saudí, las mujeres de cualquier edad pueden realizar la Umrah sin necesidad de viajar acompañadas de un Mahram masculino.']
                ]
            ],
            '/umrah-desde-barcelona' => [
                'h1' => 'Paquetes de Umrah desde Barcelona | Salidas Directas El Prat BCN y Asistencia en Badalona',
                'name' => 'Umrah desde Barcelona',
                'faqs' => [
                    ['q' => '¿Dónde está ubicada su oficina en Barcelona?', 'a' => 'Nuestra oficina está en Badalona, en la Avenida Alfonso XIII número 496 (08913 Badalona, Barcelona). Atención presencial de lunes a sábado de 9:30 a 20:30.'],
                    ['q' => '¿Qué aerolíneas vuelan desde Barcelona a Arabia Saudí?', 'a' => 'Operamos con Saudia, EgyptAir, Qatar Airways, Turkish Airlines y Emirates desde el Aeropuerto Josep Tarradellas Barcelona-El Prat (BCN).']
                ]
            ],
            '/paquetes-umrah' => [
                'h1' => 'Catálogo de Paquetes de Umrah 2026 - 2027 | Económicos, Familiares y 5★ VIP',
                'name' => 'Paquetes de Umrah',
                'faqs' => [
                    ['q' => '¿Los precios de los paquetes incluyen el vuelo?', 'a' => 'Sí, todos nuestros programas completos incluyen vuelo internacional de ida y vuelta con equipaje facturado, además del visado, hotel y traslados.'],
                    ['q' => '¿Qué tipo de habitaciones están disponibles?', 'a' => 'Disponemos de habitaciones dobles, triples, cuádruples familiares y suites frente a la Kaaba.']
                ]
            ],
            '/agencia-viajes-barcelona' => [
                'h1' => 'Agencia de Viajes en Barcelona y Badalona | Vuelos Internacionales, Umrah y Visados',
                'name' => 'Agencia de Viajes Barcelona',
                'faqs' => [
                    ['q' => '¿En qué se especializa su agencia de viajes en Barcelona?', 'a' => 'Somos especialistas líderes en billetes de avión a Pakistán, Dubái, Arabia Saudí, organización de Umrah y tramitación rápida de visados.'],
                    ['q' => '¿Ofrecen equipaje ampliado para vuelos a Pakistán?', 'a' => 'Sí, disponemos de acuerdos con aerolíneas con franquicias de 2x23 kg (46 kg) o 40 kg a destinos como Lahore, Islamabad, Karachi, Sialkot y Peshawar.']
                ]
            ],
            '/vuelos-internacionales' => [
                'h1' => 'Billetes de Avión y Vuelos Internacionales Baratos | Vuelos desde España al Mundo',
                'name' => 'Vuelos Internacionales',
                'faqs' => [
                    ['q' => '¿Qué aerolíneas ofrecen vuelos desde Barcelona y Madrid a Pakistán?', 'a' => 'Trabajamos con Qatar Airways, Emirates, Turkish Airlines, PIA y Saudia con conexiones cortas y tarifas preferenciales.'],
                    ['q' => '¿Puedo comprar billetes de ida y vuelta con fechas abiertas o flexibles?', 'a' => 'Sí, ofrecemos tarifas con flexibilidad para cambios de fecha y reemisión de billetes.']
                ]
            ],
            '/visados' => [
                'h1' => 'Gestión de Visados Internacionales en España | Arabia Saudí, EAU, UK y Schengen',
                'name' => 'Gestión de Visados',
                'faqs' => [
                    ['q' => '¿Cuánto tiempo tarda en tramitarse el visado de Arabia Saudí?', 'a' => 'El visado electrónico (e-Visa) turístico o de Umrah para residentes y ciudadanos en España se tramita habitualmente en 24 a 48 horas.'],
                    ['q' => '¿Qué necesito para solicitar el visado de Dubái?', 'a' => 'Solo copia escaneada del pasaporte con 6 meses de validez y fotografía digital reciente.']
                ]
            ],
            '/hoteles-la-meca' => [
                'h1' => 'Hoteles en La Meca y Medina | Reserva Frente al Haram y Torre del Reloj (Clock Tower)',
                'name' => 'Hoteles en La Meca y Medina',
                'faqs' => [
                    ['q' => '¿Qué hoteles quedan más cerca del Haram en La Meca?', 'a' => 'Los hoteles del complejo Abraj Al-Bait (Torre del Reloj) como Swissôtel Makkah, Pullman Zamzam y Fairmont cuentan con acceso directo a la explanada del santuario.'],
                    ['q' => '¿Puedo reservar solo el hotel sin contratar vuelos?', 'a' => 'Sí, emitimos reservas de hotel independientes con confirmación y bono oficial al instante.']
                ]
            ],
            '/umrah-2027' => [
                'h1' => 'Paquetes de Umrah 2027 desde España | Reserva Anticipada con Descuento Garantizado',
                'name' => 'Umrah 2027',
                'faqs' => [
                    ['q' => '¿Cuáles son las ventajas de reservar la Umrah 2027 con antelación?', 'a' => 'Permite conseguir ahorros de hasta un 25%, asegurar plazas para el mes sagrado de Ramadán y pagar cómodamente en cuotas mensuales.'],
                    ['q' => '¿Con qué depósito se puede reservar?', 'a' => 'Puede bloquear su plaza desde 300 € por persona y abonar el resto de forma progresiva.']
                ]
            ],
        ];

        if (! isset($spanishMeta[$path])) {
            return;
        }

        $meta = $spanishMeta[$path];
        $data['h1'] ??= $meta['h1'];
        $data['lang'] = 'es';

        if (! $data['schema_markup']) {
            $url = $base . $path;
            $siteUrl = rtrim($base, '/') . '/';

            $graph = [
                '@context' => 'https://schema.org',
                '@graph' => [
                    [
                        '@type' => 'BreadcrumbList',
                        'itemListElement' => [
                            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => $siteUrl],
                            ['@type' => 'ListItem', 'position' => 2, 'name' => $meta['name'], 'item' => $url],
                        ],
                    ],
                    [
                        '@type' => 'TravelAgency',
                        '@id' => $siteUrl . '#travelagency',
                        'name' => 'SanguGroup - Fly Planet Badalona',
                        'alternateName' => 'Sangu Travel & Tours Barcelona',
                        'url' => $url,
                        'telephone' => '+34672068148',
                        'email' => 'sangutravel@gmail.com',
                        'priceRange' => '€€',
                        'address' => [
                            '@type' => 'PostalAddress',
                            'streetAddress' => 'Av. Alfonso XIII 496',
                            'addressLocality' => 'Badalona',
                            'postalCode' => '08913',
                            'addressRegion' => 'Barcelona',
                            'addressCountry' => 'ES',
                        ],
                        'geo' => [
                            '@type' => 'GeoCoordinates',
                            'latitude' => 41.4428,
                            'longitude' => 2.2319,
                        ],
                        'openingHoursSpecification' => [
                            [
                                '@type' => 'OpeningHoursSpecification',
                                'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                                'opens' => '09:30',
                                'closes' => '20:30',
                            ],
                        ],
                    ],
                ],
            ];

            if (! empty($meta['faqs'])) {
                $faqEntities = [];
                foreach ($meta['faqs'] as $faq) {
                    $faqEntities[] = [
                        '@type' => 'Question',
                        'name' => $faq['q'],
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => $faq['a'],
                        ],
                    ];
                }
                $graph['@graph'][] = [
                    '@type' => 'FAQPage',
                    'mainEntity' => $faqEntities,
                ];
            }

            $data['schema_markup'] = (string) json_encode($graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
    }

    private function blogPostingSchema(Blog $blog, string $base): string
    {
        $url = $base . '/blog/' . $blog->slug;

        $posting = [
            '@type' => 'BlogPosting',
            'headline' => $blog->title,
            'description' => $this->clean($blog->excerpt) ?: $this->excerpt((string) $blog->content),
            'url' => $url,
            'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $url],
            'datePublished' => optional($blog->published_at ?? $blog->created_at)->toIso8601String(),
            'dateModified' => optional($blog->updated_at)->toIso8601String(),
            'author' => [
                '@type' => 'Person',
                'name' => $this->clean($blog->author) ?: self::BRAND,
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => self::BRAND,
                'logo' => ['@type' => 'ImageObject', 'url' => $base . '/images/logo.png'],
            ],
        ];

        $image = $this->absoluteImage($blog->image_url, $base);
        if ($image) {
            $posting['image'] = $image;
        }

        $graph = [
            '@context' => 'https://schema.org',
            '@graph' => [
                $posting,
                [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $base . '/'],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => $base . '/blog'],
                        ['@type' => 'ListItem', 'position' => 3, 'name' => $blog->title, 'item' => $url],
                    ],
                ],
            ],
        ];

        return (string) json_encode($graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Run a DB callback defensively so a missing table/migration can never take
     * the whole public site down (e.g. a fresh install where `blogs` does not
     * exist yet). SEO metadata is non-critical, so failures degrade gracefully.
     */
    private function safe(callable $callback): mixed
    {
        try {
            return $callback();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function normalizePath(string $path): string
    {
        $path = '/' . ltrim($path, '/');
        $path = $path === '/' ? '/' : rtrim($path, '/');

        return $path === '' ? '/' : $path;
    }

    private function canonicalBase(): string
    {
        $url = (string) config('app.url');
        $parts = parse_url($url);
        $scheme = $parts['scheme'] ?? 'https';
        $host = $parts['host'] ?? 'localhost';
        $host = preg_replace('/^www\./i', '', $host);

        return $scheme . '://' . $host;
    }

    private function isPrivate(string $path): bool
    {
        return $path === '/book'
            || str_starts_with($path, '/booking-success')
            || str_starts_with($path, '/admin')
            || str_starts_with($path, '/checkout');
    }

    private function isSafeCanonical(string $url): bool
    {
        return ! preg_match('#(localhost|\.test|127\.0\.0\.1)#i', $url);
    }

    private function finalizeCanonical(string $canonical, string $base): string
    {
        // Always re-host onto the canonical base, preserving the path, so a
        // stray host/scheme (www, http, localhost) can never leak into the tag.
        $path = parse_url(trim($canonical), PHP_URL_PATH);
        $path = '/' . ltrim((string) $path, '/');

        return $base . ($path === '/' ? '/' : rtrim($path, '/'));
    }

    private function absoluteImage(?string $image, string $base): ?string
    {
        $image = trim((string) $image);
        if ($image === '') {
            return null;
        }
        if (preg_match('#^https?://#i', $image)) {
            return $image;
        }

        return $base . '/' . ltrim($image, '/');
    }

    private function normalizeSchema($schema): ?string
    {
        if (empty($schema)) {
            return null;
        }
        if (is_array($schema)) {
            $schema = json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        $schema = trim((string) $schema);
        if ($schema === '') {
            return null;
        }

        // Strip surrounding <script> tags if an admin pasted a full block.
        $schema = preg_replace('#^\s*<script[^>]*>#i', '', $schema);
        $schema = preg_replace('#</script>\s*$#i', '', (string) $schema);

        // Unresolved Blade/placeholder markup would emit invalid JSON-LD.
        if (str_contains((string) $schema, '{{') || str_contains((string) $schema, '}}')) {
            return null;
        }
        // Basic JSON validity gate.
        json_decode((string) $schema);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        return (string) $schema;
    }

    private function clean(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return trim(preg_replace('/\s+/', ' ', $value));
    }

    private function excerpt(string $text, int $len = 160): string
    {
        $text = trim(preg_replace('/\s+/', ' ', strip_tags($text)));

        return mb_strlen($text) > $len ? mb_substr($text, 0, $len) . '…' : $text;
    }

    private function humanizeSlug(string $slug): string
    {
        return ucwords(str_replace('-', ' ', preg_replace('/-[A-Za-z0-9]{4}$/', '', $slug)));
    }
}
