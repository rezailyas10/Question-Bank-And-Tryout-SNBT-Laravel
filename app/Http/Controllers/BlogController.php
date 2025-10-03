<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
     public function index(Request $request)
    {
        $query = Blog::query();
        
        // Filter by category if provided
        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }
        
        // Search by title if provided
        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        
        $blogs = $query->latest()->paginate(12);
        
        return view('pages.blog.index', compact('blogs'));
    }
    
    /**
     * Display the specified blog.
     */
    public function show($slug)
    {
        $blog = Blog::where('slug', $slug)->firstOrFail();
        
        // Get related blogs (same category, exclude current)
        $relatedBlogs = Blog::where('category', $blog->category)
                           ->where('id', '!=', $blog->id)
                           ->latest()
                           ->limit(3)
                           ->get();
        
        return view('pages.blog.detail', compact('blog', 'relatedBlogs'));
    }
}
