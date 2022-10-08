<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use OpenTracing\GlobalTracer;

class ArticleController extends Controller
{
    public function index()
    {
        $tracer = GlobalTracer::get();
        $scope = $tracer->startActiveSpan('All Articles Request');
        $data = Article::all();
        $scope->close();
        return $data;
    }

    public function show(Article $article)
    {
        return $article;
    }

    public function store(Request $request)
    {
        $article = Article::create($request->all());
        return response()->json($article, 201);
    }

    public function update(Request $request, Article $article)
    {
        $article->update($request->all());
        return response()->json($article, 200);
    }

    public function delete(Article $article)
    {
        $article->delete();
        return response()->json(null, 204);
    }
}
