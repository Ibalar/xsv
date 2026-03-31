@extends('layouts.main')

@php
    $pageDescription = \Illuminate\Support\Str::limit(
        trim(preg_replace('/\s+/', ' ', strip_tags($page->content))),
        160,
        ''
    );

    $seo = [
        'title' => $page->title . ' - ' . config('app.name', 'XSV.BY'),
        'description' => $pageDescription,
        'canonical' => route('pages.show', $page->slug),
        'headline' => $page->title,
        'schema_type' => 'WebPage',
    ];
@endphp

@section('title', $page->title . ' - ' . config('app.name', 'XSV.BY'))

@section('content')
    <!-- Breadcrumb -->
    <x-breadcrumb :items="$breadcrumbs" />

    <!-- Page content -->
    <section class="container py-2 mt-4 mb-lg-4 mb-xl-5">
        <div class="row justify-content-center bg-body-tertiary py-3 rounded-5">
            <div class="col-lg-9">
                <h1 class="h2 pb-2 pb-sm-3">{{ $page->title }}</h1>
                <div class="page-content">
                    {!! $page->content !!}
                </div>
            </div>
        </div>
    </section>
@endsection
