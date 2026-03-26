@extends('layouts.main')

@section('title', 'Контакты - ' . config('app.name', 'XSV.BY'))

@section('content')
    <!-- Breadcrumb -->
    <x-breadcrumb :items="$breadcrumbs" />

    <!-- Contacts page -->
    <section class="container py-2 mt-4 mb-lg-4 mb-xl-5">
        <h1 class="h2 pb-2 pb-sm-3 text-center mb-4">Контакты</h1>

        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="row g-4 g-lg-3 pb-4 pb-lg-3 bg-body-tertiary">
                    <!-- Contact information -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4 p-lg-5">
                                <h2 class="h4 mb-4">Наши контакты</h2>

                                @if(!empty($contacts['phones']))
                                    <div class="mb-4">
                                        <h6 class="text-uppercase fs-xs text-muted mb-3">Телефоны</h6>
                                        @foreach($contacts['phones'] as $phone)
                                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone['number'] ?? '') }}"
                                               class="d-flex align-items-center text-decoration-none mb-2">
                                                <i class="ci-phone me-2 text-primary"></i>
                                                <span>
                                                    {{ $phone['number'] ?? '' }}
                                                    @if(!empty($phone['label'])) ({{ $phone['label'] }}) @endif
                                                </span>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif

                                @if(!empty($contacts['email']))
                                    <div class="mb-4">
                                        <h6 class="text-uppercase fs-xs text-muted mb-3">Email</h6>
                                        <a href="mailto:{{ $contacts['email'] }}" class="d-flex align-items-center text-decoration-none">
                                            <i class="ci-mail me-2 text-primary"></i>
                                            <span>{{ $contacts['email'] }}</span>
                                        </a>
                                    </div>
                                @endif

                                @if(!empty($contacts['address']))
                                    <div class="mb-4">
                                        <h6 class="text-uppercase fs-xs text-muted mb-3">Адрес</h6>
                                        <div class="d-flex align-items-start">
                                            <i class="ci-map-pin me-2 text-primary mt-1"></i>
                                            <span>{{ $contacts['address'] }}</span>
                                        </div>
                                    </div>
                                @endif

                                @if(!empty($contacts['working_hours']))
                                    <div class="mb-4">
                                        <h6 class="text-uppercase fs-xs text-muted mb-3">Режим работы</h6>
                                        <div class="d-flex align-items-start">
                                            <i class="ci-clock me-2 text-primary mt-1"></i>
                                            <span>{!! nl2br($contacts['working_hours']) !!}</span>
                                        </div>
                                    </div>
                                @endif

                                @if(!empty($contacts['social_networks']) && is_array($contacts['social_networks']))
                                    <div>
                                        <h6 class="text-uppercase fs-xs text-muted mb-3">Мы в соцсетях</h6>
                                        <div class="d-flex gap-3">
                                            @foreach($contacts['social_networks'] as $social)
                                                @if(!empty($social['url']))
                                                    <a href="{{ $social['url'] }}" target="_blank" class="btn btn-outline-secondary btn-icon btn-sm rounded-circle" title="{{ $social['name'] ?? 'Социальная сеть' }}">
                                                        <i class="{{ $social['icon'] ?? 'ci-share' }}"></i>
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Contact form -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4 p-lg-5">
                                <h2 class="h4 mb-4">Напишите нам</h2>

                                <form action="{{ route('contacts.send') }}" method="POST">
                                    @csrf

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="name" class="form-label">Имя <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   class="form-control @error('name') is-invalid @enderror"
                                                   id="name"
                                                   name="name"
                                                   value="{{ old('name') }}"
                                                   placeholder="Ваше имя"
                                                   required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="phone" class="form-label">Телефон <span class="text-danger">*</span></label>
                                            <input type="tel"
                                                   class="form-control @error('phone') is-invalid @enderror"
                                                   id="phone"
                                                   name="phone"
                                                   value="{{ old('phone') }}"
                                                   placeholder="+375 (XX) XXX-XX-XX"
                                                   required>
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email"
                                                   class="form-control @error('email') is-invalid @enderror"
                                                   id="email"
                                                   name="email"
                                                   value="{{ old('email') }}"
                                                   placeholder="example@mail.com">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <label for="message" class="form-label">Сообщение <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('message') is-invalid @enderror"
                                                      id="message"
                                                      name="message"
                                                      rows="5"
                                                      placeholder="Введите ваше сообщение..."
                                                      required>{{ old('message') }}</textarea>
                                            @error('message')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <div class="form-check">
                                                <input class="form-check-input @error('agree') is-invalid @enderror"
                                                       type="checkbox"
                                                       id="agree"
                                                       name="agree"
                                                       required>
                                                <label class="form-label fs-sm" for="agree">
                                                    Я согласен на обработку
                                                    <a href="{{ route('privacy-policy') }}" class="text-decoration-underline">персональных данных</a>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                @error('agree')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="ci-send me-2"></i>Отправить сообщение
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                @session('success')
                                    <div class="alert alert-success mt-3">
                                        <i class="ci-check-circle me-2"></i>
                                        {{ session('success') }}
                                    </div>
                                @endsession
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
