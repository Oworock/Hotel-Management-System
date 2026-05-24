<?php

namespace App\Http\Controllers;

use App\Models\FAQ;
use App\Models\Testimonial;
use App\Models\Gallery;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ContentManagementController extends Controller
{
    // FAQs Management
    public function faqs()
    {
        $faqs = FAQ::orderBy('order')->get();
        return view('admin.content.faqs', compact('faqs'));
    }

    public function storeFaq(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        FAQ::create($validated);
        return redirect()->route('admin.faqs')->with('success', 'FAQ added successfully');
    }

    public function updateFaq(Request $request, FAQ $faq)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $faq->update($validated);
        return back()->with('success', 'FAQ updated successfully');
    }

    public function deleteFaq(FAQ $faq)
    {
        $faq->delete();
        return back()->with('success', 'FAQ deleted successfully');
    }

    // Testimonials Management
    public function testimonials()
    {
        $testimonials = Testimonial::latest()->get();
        return view('admin.content.testimonials', compact('testimonials'));
    }

    public function storeTestimonial(Request $request)
    {
        $validated = $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_title' => 'nullable|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'image' => 'nullable|image|max:2048',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('testimonials', 'public');
        }

        Testimonial::create($validated);
        return back()->with('success', 'Testimonial added successfully');
    }

    public function updateTestimonial(Request $request, Testimonial $testimonial)
    {
        $validated = $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_title' => 'nullable|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'image' => 'nullable|image|max:2048',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('testimonials', 'public');
        }

        $testimonial->update($validated);
        return back()->with('success', 'Testimonial updated successfully');
    }

    public function deleteTestimonial(Testimonial $testimonial)
    {
        if ($testimonial->image) {
            \Storage::disk('public')->delete($testimonial->image);
        }
        $testimonial->delete();
        return back()->with('success', 'Testimonial deleted successfully');
    }

    // Gallery Management
    public function gallery()
    {
        $photos = Gallery::orderBy('order')->get();
        return view('admin.content.gallery', compact('photos'));
    }

    public function storeGallery(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|max:5120',
            'order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['image'] = $request->file('image')->store('gallery', 'public');
        Gallery::create($validated);
        return back()->with('success', 'Photo added successfully');
    }

    public function updateGallery(Request $request, Gallery $photo)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
            'order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            \Storage::disk('public')->delete($photo->image);
            $validated['image'] = $request->file('image')->store('gallery', 'public');
        }

        $photo->update($validated);
        return back()->with('success', 'Photo updated successfully');
    }

    public function deleteGallery(Gallery $photo)
    {
        \Storage::disk('public')->delete($photo->image);
        $photo->delete();
        return back()->with('success', 'Photo deleted successfully');
    }

    // Blog Posts Management
    public function blog()
    {
        $posts = BlogPost::with('author')->latest()->get();
        $seoColumnsReady = Schema::hasColumn('blog_posts', 'meta_title');

        return view('admin.content.blog', compact('posts', 'seoColumnsReady'));
    }

    public function storeBlog(Request $request)
    {
        $request->merge([
            'slug' => $this->seoSlug($request->input('slug') ?: $request->input('title')),
        ]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:blog_posts|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:320',
            'meta_keywords' => 'nullable|string|max:500',
            'focus_keyword' => 'nullable|string|max:120',
            'canonical_url' => 'nullable|url|max:500',
            'featured_image' => 'nullable|image|max:5120',
            'is_published' => 'boolean',
        ]);

        if (!Schema::hasColumn('blog_posts', 'meta_title')) {
            unset(
                $validated['meta_title'],
                $validated['meta_description'],
                $validated['meta_keywords'],
                $validated['focus_keyword'],
                $validated['canonical_url']
            );
        }

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }

        $validated['author_id'] = auth()->id();
        if ($request->input('is_published')) {
            $validated['published_at'] = now();
        }

        BlogPost::create($validated);
        return back()->with('success', 'Blog post created successfully');
    }

    public function updateBlog(Request $request, BlogPost $post)
    {
        $request->merge([
            'slug' => $this->seoSlug($request->input('slug') ?: $request->input('title')),
        ]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => "required|string|unique:blog_posts,slug,{$post->id}|max:255",
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:320',
            'meta_keywords' => 'nullable|string|max:500',
            'focus_keyword' => 'nullable|string|max:120',
            'canonical_url' => 'nullable|url|max:500',
            'featured_image' => 'nullable|image|max:5120',
            'is_published' => 'boolean',
        ]);

        if (!Schema::hasColumn('blog_posts', 'meta_title')) {
            unset(
                $validated['meta_title'],
                $validated['meta_description'],
                $validated['meta_keywords'],
                $validated['focus_keyword'],
                $validated['canonical_url']
            );
        }

        if ($request->hasFile('featured_image')) {
            \Storage::disk('public')->delete($post->featured_image);
            $validated['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }

        if ($request->input('is_published') && !$post->published_at) {
            $validated['published_at'] = now();
        }

        $post->update($validated);
        return back()->with('success', 'Blog post updated successfully');
    }

    public function deleteBlog(BlogPost $post)
    {
        if ($post->featured_image) {
            \Storage::disk('public')->delete($post->featured_image);
        }
        $post->delete();
        return back()->with('success', 'Blog post deleted successfully');
    }

    protected function seoSlug(?string $value): string
    {
        $stopWords = [
            'a', 'an', 'and', 'are', 'as', 'at', 'be', 'but', 'by', 'for',
            'from', 'has', 'in', 'into', 'is', 'it', 'of', 'on', 'or', 'our',
            'that', 'the', 'their', 'this', 'to', 'with', 'your', 'you',
        ];

        $words = preg_split('/\s+/', Str::lower((string) $value), -1, PREG_SPLIT_NO_EMPTY);
        $filtered = array_values(array_filter($words, fn ($word) => !in_array(trim($word, " \t\n\r\0\x0B-_,.!?"), $stopWords, true)));

        return Str::slug(implode(' ', $filtered) ?: $value);
    }
}
