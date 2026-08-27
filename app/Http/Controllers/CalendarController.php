<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function index()
    {
        $posts = Post::query()
            ->where('user_id', Auth::id())
            ->whereNotNull('scheduled_at')
            ->orderBy('scheduled_at')
            ->get();

        return view('calendar.index', compact('posts'));
    }
}
