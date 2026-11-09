<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use League\CommonMark\GithubFlavoredMarkdownConverter;

class BlogViewController extends Controller
{
    private function posts(): array
    {
        $file = public_path('wasaas-posts/index.json');
        if (! is_file($file)) {
            return [];
        }
        $posts = json_decode((string) file_get_contents($file), true);

        return is_array($posts) ? $posts : [];
    }

    /** The SEO module content sometimes contains literal separator artifacts. */
    private function cleanMarkdown(string $md): string
    {
        $md = preg_replace('~\\\\_{2,}~', '', $md);
        $md = str_replace('\\', '', $md);

        return $md;
    }

    private function toHtml(string $md): string
    {
        $converter = new GithubFlavoredMarkdownConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        return (string) $converter->convert($this->cleanMarkdown($md));
    }

    private function excerpt(string $md, int $len = 190): string
    {
        $text = trim(preg_replace('/\s+/', ' ', strip_tags($this->toHtml($md))));

        return mb_strlen($text) > $len ? mb_substr($text, 0, $len).'…' : $text;
    }

    public function index()
    {
        $posts = $this->posts();
        usort($posts, fn ($a, $b) => strcmp((string) ($b['publishedAt'] ?? ''), (string) ($a['publishedAt'] ?? '')));
        $posts = array_map(function ($p) {
            $p['excerpt'] = $this->excerpt($p['content'] ?? '');
            $p['date'] = date('M j, Y', strtotime((string) ($p['publishedAt'] ?? 'now')));

            return $p;
        }, $posts);

        return view('blog.index', [
            'title' => 'Blog',
            'description' => 'Travel guides, Umrah tips and the latest articles from SanguGroup.',
            'posts' => $posts,
        ]);
    }

    public function show(Request $request, $slug)
    {
        $post = null;
        foreach ($this->posts() as $p) {
            if (($p['slug'] ?? '') === $slug) {
                $post = $p;
                break;
            }
        }
        abort_unless($post, 404);

        $title = $post['title'] ?? $slug;
        $description = $post['metaDescription'] ?? $this->excerpt($post['content'] ?? '');
        $canonical = url('/blog/'.$slug);

        return view('blog.show', [
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'post' => $post,
            'contentHtml' => $this->toHtml($post['content'] ?? ''),
            'excerpt' => $this->excerpt($post['content'] ?? '', 220),
            'publishedAt' => date('M j, Y', strtotime((string) ($post['publishedAt'] ?? 'now'))),
        ]);
    }
}