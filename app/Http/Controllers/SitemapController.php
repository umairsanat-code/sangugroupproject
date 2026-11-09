<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\UmrahPackage;
use App\Models\Visa;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    public function index(Request $request)
    {
        $base = rtrim((string) config('app.url'), '/');

        $urls = [];

        $add = function (string $path, ?string $lastmod = null, float $priority = 0.5, string $freq = 'weekly') use (&$urls, $base) {
            $path = trim($path);

            if ($path === '' || $path === '/') {
                $full = $base . '/';
            } else {
                $path = '/' . ltrim($path, '/');
                $path = rtrim($path, '/');
                $full = $base . $path;
            }

            $urls[$full] = [
                'loc' => $full,
                'lastmod' => $lastmod,
                'priority' => $priority,
                'freq' => $freq,
            ];
        };

        $today = now()->toDateString();

        // ── Static public pages ────────────────────────────────────────────
        $staticPages = [
            ['/', 1.0, 'daily'],
            ['/about', 0.6, 'monthly'],
            ['/services', 0.6, 'monthly'],
            ['/team', 0.5, 'monthly'],
            ['/flights', 0.5, 'weekly'],
            ['/visa', 0.8, 'daily'],
            ['/umrah', 0.9, 'daily'],
            ['/hotels', 0.7, 'daily'],
            ['/gallery', 0.5, 'weekly'],
            ['/esim', 0.6, 'weekly'],
            ['/blog', 0.8, 'daily'],
            ['/contact', 0.6, 'monthly'],
            // Spanish SEO landing pages
            ['/umrah-desde-espana', 0.9, 'daily'],
            ['/umrah-desde-barcelona', 0.9, 'daily'],
            ['/paquetes-umrah', 0.9, 'daily'],
            ['/agencia-viajes-barcelona', 0.9, 'daily'],
            ['/vuelos-internacionales', 0.8, 'weekly'],
            ['/visados', 0.8, 'weekly'],
            ['/hoteles-la-meca', 0.8, 'weekly'],
            ['/umrah-2027', 0.8, 'weekly'],
        ];
        foreach ($staticPages as [$page, $priority, $freq]) {
            $add($page, $today, $priority, $freq);
        }

        // ── Active blog posts from the database ───────────────────────────
        try {
            foreach (Blog::where('is_active', true)->get() as $blog) {
                $lastmod = ($blog->published_at ?? $blog->updated_at);
                $add('/blog/' . $blog->slug, optional($lastmod)->toDateString(), 0.7, 'monthly');
            }
        } catch (\Throwable $e) {
            // Graceful fallback if blogs table is absent
        }

        // ── SEO-module blog posts (webhook-published posts) ───────────────
        // Merged with the DB blogs and deduplicated by URL, so no URL is repeated.
        $jsonFile = public_path('wasaas-posts/index.json');
        if (is_file($jsonFile)) {
            $posts = json_decode((string) file_get_contents($jsonFile), true);
            if (is_array($posts)) {
                foreach ($posts as $post) {
                    if (empty($post['slug'])) {
                        continue;
                    }
                    $lastmod = null;
                    if (! empty($post['publishedAt'])) {
                        $lastmod = substr((string) $post['publishedAt'], 0, 10);
                    }
                    $add('/blog/' . $post['slug'], $lastmod, 0.7, 'monthly');
                }
            }
        }

        // ── Visas ─────────────────────────────────────────────────────────
        try {
            foreach (Visa::all() as $visa) {
                $add('/visa/' . ltrim($visa->slug, '/'), optional($visa->updated_at)->toDateString(), 0.7, 'weekly');
            }
        } catch (\Throwable $e) {
            // Graceful fallback if visas table is absent
        }

        // ── Umrah packages (frontend uses the slug in /umrah/{slug}) ──────
        try {
            foreach (UmrahPackage::all() as $package) {
                if (empty($package->slug)) {
                    continue;
                }
                $add('/umrah/' . ltrim($package->slug, '/'), optional($package->updated_at)->toDateString(), 0.8, 'weekly');
            }
        } catch (\Throwable $e) {
            // Graceful fallback if umrah_packages table is absent
        }

        // ── Build the XML document ────────────────────────────────────────
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $u) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . htmlspecialchars($u['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</loc>' . "\n";
            if ($u['lastmod']) {
                $xml .= '    <lastmod>' . e($u['lastmod']) . '</lastmod>' . "\n";
            }
            $xml .= '    <changefreq>' . e($u['freq']) . '</changefreq>' . "\n";
            $xml .= '    <priority>' . number_format((float) $u['priority'], 1) . '</priority>' . "\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>' . "\n";

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('X-Content-Type-Options', 'nosniff');
    }
}