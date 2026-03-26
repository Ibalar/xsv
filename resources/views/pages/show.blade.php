@extends('layouts.main')

@section('title', $page->title . ' - ' . config('app.name', 'XSV.BY'))

@section('content')
    <!-- Breadcrumb -->
    <x-breadcrumb :items="$breadcrumbs" />

    <!-- Page content -->
    <section class="container py-5 mt-4 mb-lg-4 mb-xl-5">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <h1 class="h2 pb-2 pb-sm-3">{{ $page->title }}</h1>
                <div class="page-content">
                    {!! $page->content !!}
                </div>
            </div>
        </div>
    </section>
@endsection
