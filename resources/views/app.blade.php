<!DOCTYPE html>
<html lang="{{ $seo->lang ?? 'en' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $seoTitle = $seo->title ?? 'Sangu Travel & Tours | Flights, Visa, Umrah & Hotel Booking';
        $seoDescription = $seo->description ?? 'Flights, hotels, Umrah packages and visa services worldwide — arranged by Sangu Travel & Tours.';
        $seoCanonical = $seo->canonical_url ?? url()->current();
        $seoRobots = $seo->robots ?? 'index, follow';
        $seoOgImage = $seo->og_image ?? ($seo->site_url ?? url('/')) . 'images/logo.png';
        $seoOgType = $seo->og_type ?? 'website';
        $seoSiteUrl = rtrim($seo->site_url ?? url('/'), '/') . '/';
        $seoBrand = $seo->brand ?? 'Sangu Travel & Tours';
        $seoBrandAlt = $seo->brand_alt ?? 'SanguGroup';

        // Site-level structured data, built as an array so no "@" directive
        // (e.g. Laravel 12's @context) is accidentally parsed by Blade.
        $siteGraph = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'Organization',
                    '@id' => $seoSiteUrl . '#organization',
                    'name' => $seoBrand,
                    'alternateName' => $seoBrandAlt,
                    'url' => $seoSiteUrl,
                    'logo' => ['@type' => 'ImageObject', 'url' => $seoSiteUrl . 'images/logo.png'],
                ],
                [
                    '@type' => 'TravelAgency',
                    '@id' => $seoSiteUrl . '#travelagency',
                    'name' => $seoBrand . ' - Fly Planet Badalona',
                    'alternateName' => $seoBrandAlt,
                    'url' => $seoSiteUrl,
                    'logo' => ['@type' => 'ImageObject', 'url' => $seoSiteUrl . 'images/logo.png'],
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
                ],
                [
                    '@type' => 'WebSite',
                    '@id' => $seoSiteUrl . '#website',
                    'url' => $seoSiteUrl,
                    'name' => $seoBrand,
                    'publisher' => ['@id' => $seoSiteUrl . '#organization'],
                    'inLanguage' => ['en', 'es'],
                ],
            ],
        ];
    @endphp

    <!-- Dynamic SEO -->
    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="robots" content="{{ $seoRobots }}">
    <script>window.__SEO_H1__ = @json($seo->h1 ?? null);</script>
    <link rel="icon" type="image/png" href="/images/logo.png">
    <link rel="shortcut icon" href="/images/logo.png">
    <link rel="apple-touch-icon" href="/images/logo.png">

    <link rel="canonical" href="{{ $seoCanonical }}">
    <link rel="alternate" hreflang="es" href="{{ url('/umrah-desde-espana') }}">
    <link rel="alternate" hreflang="en" href="{{ url('/') }}">
    <link rel="alternate" hreflang="x-default" href="{{ url('/') }}">

    <!-- Open Graph -->
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:image" content="{{ $seoOgImage }}">
    <meta property="og:type" content="{{ $seoOgType }}">
    <meta property="og:url" content="{{ $seoCanonical }}">
    <meta property="og:site_name" content="{{ $seoBrand }}">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $seoOgImage }}">

    <!-- Site-level structured data (Organization + WebSite) -->
    <script type="application/ld+json">{!! json_encode($siteGraph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

    @if(!empty($seo->schema_markup))
    <!-- Page structured data (admin-managed) -->
    <script type="application/ld+json">
    {!! $seo->schema_markup !!}
    </script>
    @endif


    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-NW9EYEJNWW"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-NW9EYEJNWW');
    </script>

    <!-- Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app"></div>
    <script src="/assets/js/data.js"></script>
    <script src="/assets/js/main.js"></script>
</body>
</html>
