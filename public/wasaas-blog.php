<?php
/**
 * WaSaaS Auto-Publish webhook endpoint (universal)
 * -------------------------------------------------
 * Drop this file into ANY PHP site (plain PHP, WordPress mu-plugin, etc.)
 * and set it as the "Webhook URL" in your WaSaaS SEO -> Publish Settings.
 *
 * The webhook secret you set in WaSaaS must match the SECRET constant below.
 *
 * It saves every published article as a markdown file in ./wasaas-posts/
 * and appends to an index.json so your own blog/list code can read them.
 *
 * Requires PHP 7.1+ (JSON). No database needed for file mode.
 *
 * For LARAVEL: see the commented controller example at the bottom of this file.
 */

const SECRET = 'nDGoqg44ZW90gsFB2qeQlfeMztK_Saxy';
const POSTS_DIR = __DIR__ . '/wasaas-posts';

// ---------------------------------------------------------------------------
// Only allow POST
// ---------------------------------------------------------------------------
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
    exit;
}

// ---------------------------------------------------------------------------
// Read body
// ---------------------------------------------------------------------------
$raw = file_get_contents('php://input');
$data = json_decode($raw ?: '{}', true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid JSON body']);
    exit;
}

// ---------------------------------------------------------------------------
// Verify secret (Authorization: Bearer <secret> OR body secret)
// ---------------------------------------------------------------------------
$headers = function_exists('getallheaders') ? getallheaders() : [];
$auth = $headers['Authorization'] ?? '';
$bearer = preg_match('/^Bearer\s+(.+)$/i', $auth, $m) ? trim($m[1]) : '';
$given = $bearer !== '' ? $bearer : (string)($data['secret'] ?? '');
if (!hash_equals(SECRET, $given)) {
    http_response_code(401);
    echo json_encode(['message' => 'Invalid or missing secret']);
    exit;
}

// ---------------------------------------------------------------------------
// Build a safe slug + post record
// ---------------------------------------------------------------------------
$title = trim((string)($data['title'] ?? ''));
if ($title === '') {
    http_response_code(400);
    echo json_encode(['message' => 'Missing title']);
    exit;
}
$slug = trim((string)($data['slug'] ?? ''));
if ($slug === '') {
    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $title));
    $slug = trim($slug, '-');
}
$slug = preg_replace('/[^a-z0-9-]/i', '', $slug) ?: 'post-' . time();

$record = [
    'slug'         => $slug,
    'title'        => $title,
    'content'      => (string)($data['content'] ?? ''),
    'metaTitle'    => (string)($data['metaTitle'] ?? ''),
    'metaDescription' => (string)($data['metaDescription'] ?? ''),
    'publishedAt'  => (string)($data['publishedAt'] ?? gmdate('c')),
    'siteUrl'      => (string)($data['siteUrl'] ?? ''),
];

if (!is_dir(POSTS_DIR)) {
    @mkdir(POSTS_DIR, 0775, true);
}

// Save the markdown file so any static/blog code can include it.
file_put_contents(POSTS_DIR . '/' . $slug . '.md', $record['content']);

// Append to index.json (keeps insertion order + list of all posts).
$indexFile = POSTS_DIR . '/index.json';
$index = [];
if (is_file($indexFile)) {
    $index = json_decode((string)file_get_contents($indexFile), true) ?: [];
}
// Deduplicate by slug (re-publishing updates the existing entry).
$index = array_values(array_filter($index, fn($p) => ($p['slug'] ?? '') !== $slug));
$index[] = $record;
file_put_contents($indexFile, json_encode($index, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'postId'  => $slug,
    'url'     => $record['siteUrl'] !== '' ? rtrim($record['siteUrl'], '/') . '/wasaas-posts/' . $slug . '.md' : null,
]);
exit;

/**
 * -------------------------------------------------------------
 * LARAVEL EXAMPLE (put this in routes/web.php or an api route):
 * -------------------------------------------------------------
 *
 * Route::post('/wasaas/publish', function (Request $request) {
 *     $secret = env('WASAAS_WEBHOOK_SECRET');
 *     $auth = $request->header('Authorization', '');
 *     $bearer = Str::startsWith($auth, 'Bearer ') ? substr($auth, 7) : '';
 *     $given = $bearer !== '' ? $bearer : $request->input('secret');
 *     if (! $secret || ! hash_equals($secret, (string) $given)) {
 *         return response()->json(['message' => 'Unauthorized'], 401);
 *     }
 *     $post = App\Models\Post::updateOrCreate(
 *         ['slug' => $request->input('slug', Str::slug($request->input('title')))],
 *         [
 *             'title'       => $request->input('title'),
 *             'content'     => $request->input('content'),       // markdown
 *             'meta_title'  => $request->input('metaTitle'),
 *             'meta_description' => $request->input('metaDescription'),
 *             'status'      => 'published',
 *             'published_at' => now(),
 *         ],
 *     );
 *     return response()->json(['success' => true, 'postId' => $post->id, 'url' => route('posts.show', $post)]);
 * });
 */
