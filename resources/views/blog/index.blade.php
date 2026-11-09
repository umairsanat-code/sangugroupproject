<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | SanguGroup</title>
    <meta name="description" content="{{ $description }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/blog') }}">
    <link rel="icon" type="image/png" href="/images/logo.png">
    <meta property="og:title" content="SanguGroup Blog">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/blog') }}">
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
        body { background:var(--bg); color:var(--ink); font-family:'Plus Jakarta Sans',sans-serif; line-height:1.6; }
        a { color:var(--teal); text-decoration:none; }
        a:hover { color:var(--brick); }
        .wrap { max-width:var(--maxw); margin:0 auto; padding:0 24px; }
        header.site { padding:26px 0; border-bottom:1px solid var(--line); }
        .brand { font-family:'Fraunces',serif; font-size:22px; font-weight:700; color:var(--teal); letter-spacing:-0.02em; }
        .brand span { color:var(--gold); }
        nav.top { display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap; }
        nav.top .links { display:flex; gap:22px; font-size:14px; font-weight:600; }
        main { padding:56px 0 72px; }
        .page-title { font-family:'Fraunces',serif; font-size:clamp(36px,5vw,54px); font-weight:600; letter-spacing:-0.02em; margin-bottom:12px; }
        .page-sub { color:var(--ink-soft); max-width:640px; margin-bottom:44px; }
        .grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(320px,1fr)); gap:22px; }
        .card { background:var(--bg-raised); border:1px solid var(--line); border-radius:18px; padding:26px; box-shadow:0 12px 32px -20px rgba(20,36,32,0.4); display:flex; flex-direction:column; gap:12px; transition:transform .18s ease, box-shadow .18s ease; }
        .card:hover { transform:translateY(-3px); box-shadow:0 18px 40px -20px rgba(20,36,32,0.45); }
        .card .date { font-size:12px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--gold-dark); }
        .card h2 { font-family:'Fraunces',serif; font-size:22px; line-height:1.25; }
        .card h2 a { color:var(--ink); }
        .card h2 a:hover { color:var(--teal); }
        .card p { color:var(--ink-soft); font-size:14px; }
        .card .more { margin-top:auto; font-weight:700; font-size:14px; }
        .empty { padding:48px; text-align:center; color:var(--ink-soft); background:var(--bg-raised); border:1px dashed var(--line); border-radius:18px; }
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
            <h1 class="page-title">{{ $title }}</h1>
            <p class="page-sub">{{ $description }}</p>

            @if (count($posts) > 0)
                <div class="grid">
                    @foreach ($posts as $post)
                        <a class="card" href="{{ url('/blog/'.$post['slug']) }}">
                            <span class="date">{{ $post['date'] ?? '' }}</span>
                            <h2>{{ $post['title'] }}</h2>
                            <p>{{ $post['excerpt'] ?? '' }}</p>
                            <span class="more">Read article →</span>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="empty">No articles published yet. Check back soon.</div>
            @endif
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