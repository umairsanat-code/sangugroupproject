<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index()
    {
        $all = request()->query('all');
        if ($all) {
            return response()->json(Blog::orderBy('display_order')->orderBy('published_at', 'desc')->get());
        }
        return response()->json(Blog::where('is_active', true)->orderBy('display_order')->orderBy('published_at', 'desc')->get());
    }

    public function show($slug)
    {
        $blog = Blog::where('slug', $slug)->orWhere('id', $slug)->firstOrFail();
        return response()->json($blog);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'tag'            => 'nullable|string|max:100',
            'excerpt'        => 'nullable|string|max:500',
            'content'        => 'nullable|string',
            'author'         => 'nullable|string|max:255',
            'is_active'      => 'nullable|boolean',
            'display_order'  => 'nullable|integer',
            'published_at'   => 'nullable|date',
            'image'          => 'nullable|image|max:8192',
        ]);

        $data['slug'] = Str::slug($data['title']) . '-' . Str::random(4);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('blogs', 'public');
            $data['image_url'] = '/storage/' . $path;
        }

        unset($data['image']);

        if (empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $blog = Blog::create($data);
        return response()->json(['success' => true, 'blog' => $blog], 201);
    }

    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        $data = $request->validate([
            'title'          => 'sometimes|required|string|max:255',
            'tag'            => 'nullable|string|max:100',
            'excerpt'        => 'nullable|string|max:500',
            'content'        => 'nullable|string',
            'author'         => 'nullable|string|max:255',
            'is_active'      => 'nullable|boolean',
            'display_order'  => 'nullable|integer',
            'published_at'   => 'nullable|date',
            'image'          => 'nullable|image|max:8192',
        ]);

        if ($request->hasFile('image')) {
            if ($blog->image_url) {
                $oldPath = str_replace('/storage/', '', $blog->image_url);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image')->store('blogs', 'public');
            $data['image_url'] = '/storage/' . $path;
        }

        unset($data['image']);
        $blog->update($data);

        return response()->json(['success' => true, 'blog' => $blog->fresh()]);
    }

    public function toggleActive($id)
    {
        $blog = Blog::findOrFail($id);
        $blog->is_active = !$blog->is_active;
        $blog->save();
        return response()->json(['success' => true, 'is_active' => $blog->is_active]);
    }

    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);
        if ($blog->image_url) {
            $path = str_replace('/storage/', '', $blog->image_url);
            Storage::disk('public')->delete($path);
        }
        $blog->delete();
        return response()->json(['success' => true]);
    }
}
