<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | SanguGroup</title>
    <meta name="description" content="{{ $description }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $canonical }}">
    <link rel="icon" type="image/png" href="/images/logo.png">
    <meta property="og:title" content="{{ $post['metaTitle'] ?? $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:site_name" content="SanguGroup">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $post['metaTitle'] ?? $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@type": "BlogPosting",
        "headline": {{ json_encode($post['title'] ?? $title) }},
        "datePublished": {{ json_encode($post['publishedAt'] ?? date('c')) }},
        "description": {{ json_encode($description) }},
        "url": {{ json_encode($canonical) }},
        "publisher": { "@type": "Organization", "name": "SanguGroup" }
    }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink:#142420; --ink-soft:#3d4d47; --bg:#EFEAE0; --bg-raised:#F8F5EE;
            --line:#DCD5C4; --teal:#0E4B44; --teal-dark:#0A3733; --gold:#C69A3E;
            --gold-dark:#a17e2f; --brick:#B5563C; --maxw:1240px;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { background:var(--bg); color:var(--ink); font-family:'Plus Jakarta Sans',sans-serif; line-height:1.7; }
        a { color:var(--teal); text-decoration:none; }
        a:hover { color:var(--brick); }
        .wrap { max-width:var(--maxw); margin:0 auto; padding:0 24px; }
        header.site { padding:26px 0; border-bottom:1px solid var(--line); }
        .brand { font-family:'Fraunces',serif; font-size:22px; font-weight:700; color:var(--teal); letter-spacing:-0.02em; }
        .brand span { color:var(--gold); }
        nav.top { display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap; }
        nav.top .links { display:flex; gap:22px; font-size:14px; font-weight:600; }
        main { padding:52px 0 72px; }
        .article { max-width:820px; margin:0 auto; background:var(--bg-raised); border:1px solid var(--line); border-radius:22px; padding:clamp(28px,5vw,56px); }
        .crumb { font-size:13px; font-weight:700; letter-spacing:.05em; text-transform:uppercase; color:var(--gold-dark); margin-bottom:18px; }
        h1 { font-family:'Fraunces',serif; font-weight:600; font-size:clamp(32px,4.5vw,46px); line-height:1.15; letter-spacing:-0.02em; margin-bottom:16px; }
        .meta { display:flex; gap:14px; align-items:center; color:var(--ink-soft); font-size:13px; padding-bottom:24px; border-bottom:1px solid var(--line); margin-bottom:28px; }
        .prose { font-size:16.5px; }
        .prose > * + * { margin-top:18px; }
        .prose h2, .prose h3 { font-family:'Fraunces',serif; font-weight:600; margin-top:34px; line-height:1.3; }
        .prose h2 { font-size:28px; }
        .prose h3 { font-size:22px; }
        .prose a { text-decoration:underline; text-underline-offset:3px; font-weight:600; }
        .prose ul, .prose ol { padding-left:26px; }
        .prose li { margin-top:8px; }
        .prose blockquote { border-left:4px solid var(--gold); background:var(--bg); padding:16px 22px; border-radius:0 14px 14px 0; color:var(--ink-soft); font-style:italic; }
        .prose code { font-family:'IBM Plex Mono',monospace; background:var(--bg); border:1px solid var(--line); border-radius:6px; padding:1px 6px; font-size:.9em; }
        .prose pre { background:var(--teal-dark); color:#e9e2d3; padding:20px; border-radius:14px; overflow-x:auto; font-size:13.5px; }
        .prose pre code { background:none; border:none; color:inherit; padding:0; }
        .prose img { max-width:100%; border-radius:14px; }
        .prose hr { border:none; border-top:1px solid var(--line); margin:30px 0; }
        .cta { margin-top:38px; padding-top:28px; border-top:1px solid var(--line); }
        .cta a { display:inline-flex; align-items:center; gap:10px; background:var(--teal); color:#fff; font-weight:700; font-size:15px; padding:14px 26px; border-radius:999px; }
        .cta a:hover { background:var(--teal-dark); color:#fff; }
        .back { display:inline-flex; gap:8px; font-weight:700; font-size:14px; margin-top:28px; }
        footer.site { border-top:1px solid var(--line); padding:28px 0; color:var(--ink-soft); font-size:13px; }
        footer.site .wrap { display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap; }
    </style>
</head>
<body>
    <header class="site">
        <div class="wrap">
            <nav class="top">
                <a class="brand" href="{{ url('/') }}">Sangu<span>Group</span></a>
                <div class="links">
                    <a href="{{ url('/') }}">Home</a>
                    <a href="{{ url('/blog') }}">Blog</a>
                </div>
            </nav>
        </div>
    </header>

    <main>
        <div class="wrap">
            <article class="article">
                <div class="crumb"><a href="{{ url('/blog') }}">← SanguGroup Blog</a></div>
                <h1>{{ $post['title'] }}</h1>
                <div class="meta">
                    <span>{{ $publishedAt }}</span>
                    @if (!empty($post['metaTitle']))
                        <span>·</span>
                        <span>{{ $post['metaTitle'] }}</span>
                    @endif
                </div>
                <div class="prose">{!! $contentHtml !!}</div>
                <div class="cta">
                    <a href="{{ url('/') }}">Planning a trip? Talk to SanguGroup →</a>
                </div>
                <a class="back" href="{{ url('/blog') }}">← All articles</a>
            </article>
        </div>
    </main>

    <footer class="site">
        <div class="wrap">
            <span>© {{ date('Y') }} SanguGroup. All rights reserved.</span>
            <span>Travel &amp; Umrah services worldwide.</span>
        </div>
    </footer>
</body>
</html>