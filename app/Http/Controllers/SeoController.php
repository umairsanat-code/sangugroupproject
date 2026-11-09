<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SeoMetadata;

class SeoController extends Controller
{
    public function load(Request $request)
    {
        $request->validate(['path' => 'required|string']);
        
        $seo = SeoMetadata::where('path', $request->path)->first();
        
        return response()->json($seo ?: ['path' => $request->path]);
    }

    public function save(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
            'h1' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'canonical_url' => 'nullable|url',
            'schema_markup' => 'nullable|string'
        ]);

        $seo = SeoMetadata::updateOrCreate(
            ['path' => $request->path],
            [
                'h1' => $request->h1,
                'title' => $request->title,
                'description' => $request->description,
                'canonical_url' => $request->canonical_url,
                'schema_markup' => $request->schema_markup
            ]
        );

        return response()->json(['message' => 'SEO metadata saved successfully', 'seo' => $seo]);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'path' => 'required|string'
        ]);

        $content = strip_tags($request->input('content'));
        $words = str_word_count($content, 1);
        $words = array_filter($words, function($w) { return strlen($w) > 4; });
        $freq = array_count_values($words);
        arsort($freq);
        $keywords = array_slice(array_keys($freq), 0, 3);
        $kwString = ucwords(implode(', ', $keywords));

        // Determine Page Type heuristics
        $isVisa = str_contains($request->path, 'visa');
        $isUmrah = str_contains($request->path, 'umrah');
        
        // Construct optimized H1
        if ($isVisa) {
            $h1 = "Premium {$kwString} Visa Services";
        } elseif ($isUmrah) {
            $h1 = "Luxury {$kwString} Packages";
        } else {
            $h1 = "{$kwString}";
        }
        $h1 = trim($h1);
        if (strlen($h1) > 60) {
            $h1 = substr($h1, 0, 57) . '...';
        }

        // Construct optimized Title
        if ($isVisa) {
            $title = "Premium Visa Services for {$kwString} | SanguGroup Travel";
        } elseif ($isUmrah) {
            $title = "Luxury Umrah Packages {$kwString} | SanguGroup Travel";
        } else {
            $title = "{$kwString} | SanguGroup Global Travel Services";
        }
        
        // Cap title length
        if (strlen($title) > 60) {
            $title = substr($title, 0, 57) . '...';
        }

        // Construct optimized Description
        $excerpt = substr(preg_replace('/\s+/', ' ', $content), 0, 100);
        $description = "Book your next journey with SanguGroup. Discover our {$kwString} services. {$excerpt}... Experience premium travel and exceptional support.";
        if (strlen($description) > 160) {
            $description = substr($description, 0, 157) . '...';
        }

        // Construct Schema
        $schema = [
            "@context" => "https://schema.org",
            "@type" => "WebPage",
            "name" => $title,
            "description" => $description,
            "url" => url($request->path)
        ];

        return response()->json([
            'h1' => $h1,
            'title' => $title,
            'description' => $description,
            'canonical_url' => url($request->path),
            'schema_markup' => json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        ]);
    }
}
