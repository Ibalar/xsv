<!-- Page footer -->
<footer class="footer position-relative bg-dark border-top">
    <span class="position-absolute top-0 start-0 w-100 h-100 bg-body d-none d-block-dark"></span>
    <div class="container position-relative z-1 pt-sm-2 pt-md-3 pt-lg-4" data-bs-theme="dark">

        <!-- Columns with links that are turned into accordion on screens < 500px wide (sm breakpoint) -->
        <div class="accordion py-5" id="footerLinks">
            <div class="row">
                <div class="col-md-5 d-sm-flex flex-md-column align-items-center align-items-md-start pb-3 mb-sm-4">
                    <h4 class="mb-sm-0 mb-md-4 me-4">
                        <span class="text-dark-emphasis text-decoration-none">Наши контакты</span>
                    </h4>
                    @if(!empty($siteSettings['phones']['value']))
                        @foreach($siteSettings['phones']['value'] as $phone)
                            <p class="text-body fs-md text-sm-end text-md-start mb-sm-0 mb-md-3 ms-0 ms-sm-auto ms-md-0 me-4">
                                <a class="nav-link animate-target text-white fw-semibold p-0 px-1" href="tel:{{ preg_replace('/[^\d+]/', '', $phone['number'] ?? '') }}"><i class="ci-phone fs-md m-xxl-1"></i> {{ $phone['number'] ?? '' }}</a>
                            </p>
                        @endforeach
                    @else
                        {{-- Fallback на хардкод если настройки не заполнены --}}
                        <p class="text-body fs-md text-sm-end text-md-start mb-sm-0 mb-md-3 ms-0 ms-sm-auto ms-md-0 me-4">
                            <a class="nav-link animate-target text-white fw-semibold p-0 px-1" href="tel:+375296403709"><i class="ci-phone fs-md m-xxl-1"></i> +375 (29) 640-37-09</a>
                        </p>
                        <p class="text-body fs-md text-sm-end text-md-start mb-sm-0 mb-md-3 ms-0 ms-sm-auto ms-md-0 me-4">
                            <a class="nav-link animate-target text-white fw-semibold p-0 px-1" href="tel:+375445913335"><i class="ci-phone fs-md m-xxl-1"></i> +375 (44) 591-33-35</a>
                        </p>
                        <p class="text-body fs-md text-sm-end text-md-start mb-sm-0 mb-md-3 ms-0 ms-sm-auto ms-md-0 me-4">
                            <a class="nav-link animate-target text-white fw-semibold p-0 px-1" href="tel:+375295913372"><i class="ci-phone fs-md m-xxl-1"></i> +375 (29) 591-33-72</a>
                        </p>
                    @endif
                    <p class="text-body fs-md text-sm-end text-md-start mb-sm-0 mb-md-3 ms-0 ms-sm-auto ms-md-0 me-4">
                        <a class="nav-link animate-target text-white fw-semibold p-0 px-1" href="mailto:{{ $siteSettings['email'] ?? 'xsv.by@yandex.by' }}"><i class="ci-mail fs-md m-xxl-1"></i> {{ $siteSettings['email'] ?? 'xsv.by@yandex.by' }}</a>
                    </p>
                    <p class="text-body fs-md text-sm-end text-md-start mb-sm-0 mb-md-3 ms-0 ms-sm-auto ms-md-0 me-4">
                        <i class="ci-map-pin fs-md m-xxl-1"></i> {!! nl2br(e($siteSettings['address'] ?? "Минская обл., Логойский р-н\n д. Зелёный сад, ул. Подлесная, 20")) !!}
                    </p>
                </div>
                <div class="col-md-5 d-sm-flex flex-md-column align-items-center align-items-md-start pb-3 mb-sm-4">
                    <h4 class="mb-sm-0 mb-md-4 me-4">
                        <span class="text-dark-emphasis text-decoration-none">Наши реквизиты</span>
                    </h4>
                    @if(!empty($siteSettings['business_info']['value']))
                        @php
                            $business = $siteSettings['business_info']['value'];
                        @endphp
                        <p class="text-body fs-md text-sm-end text-md-start mb-sm-0 mb-md-3 ms-0 ms-sm-auto ms-md-0 me-4">{{ $business['company_name'] ?? 'ООО "Сказочный сад"' }}</p>
                        <p class="text-body fs-sm text-sm-end text-md-start mb-sm-0 mb-md-3 ms-0 ms-sm-auto ms-md-0 me-4">
                            УНП: {{ $business['unp'] ?? '690876969' }}<br>
                            {{ $business['additional_info'] ?? 'Свидетельство о регистрации выдано Логойским райисполком от 24.12.2025 г.' }}
                        </p>
                        @if(!empty($business['bank_account']))
                            <p class="text-body fs-sm text-sm-end text-md-start mb-sm-0 mb-md-3 ms-0 ms-sm-auto ms-md-0 me-4">
                                Р/сч: {{ $business['bank_account'] }}<br>
                                в {{ $business['bank_name'] ?? 'ЗАО БСБ Банк' }} {{ $business['bank_address'] ?? '220004, г. Минск, пр. Победителей, 23, корп. 3' }}<br>
                                код {{ $business['bank_code'] ?? 'UNBSBY2X' }}
                            </p>
                        @endif
                    @else
                        {{-- Fallback на хардкод если настройки не заполнены --}}
                        <p class="text-body fs-md text-sm-end text-md-start mb-sm-0 mb-md-3 ms-0 ms-sm-auto ms-md-0 me-4">ООО "Сказочный сад"</p>
                        <p class="text-body fs-sm text-sm-end text-md-start mb-sm-0 mb-md-3 ms-0 ms-sm-auto ms-md-0 me-4">УНП: 690876969<br>
                            Свидетельство о регистрации<br>
                            выдано Логойским райисполком от 24.12.2025 г.</p>
                        <p class="text-body fs-sm text-sm-end text-md-start mb-sm-0 mb-md-3 ms-0 ms-sm-auto ms-md-0 me-4">Р/сч: BY85 UNBS 3012 2578 3000 0000 0933<br>
                            в ЗАО БСБ Банк 220004, г. Минск, пр. Победителей, 23, корп. 3<br>
                            код UNBSBY2X</p>
                    @endif
                </div>
                <div class="col-md-2">
                    <div class="accordion-item col border-0">
                        <h6 class="accordion-header" id="companyHeading">
                            <span class="text-dark-emphasis d-none d-sm-block">Разделы каталога</span>
                            <button type="button" class="accordion-button collapsed py-3 d-sm-none" data-bs-toggle="collapse" data-bs-target="#companyLinks" aria-expanded="false" aria-controls="companyLinks">Разделы каталога</button>
                        </h6>
                        <div class="accordion-collapse collapse show d-sm-block" id="companyLinks" aria-labelledby="companyHeading" data-bs-parent="#footerLinks">
                            <ul class="nav flex-column gap-2 pt-sm-3 pb-3 mt-n1 mb-1">
                                @foreach($footerCategories as $category)
                                    <li class="d-flex w-100 pt-1">
                                        <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0"
                                           href="{{ route('catalog.show', $category->slug) }}">
                                            {{ $category->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <hr class="d-sm-none my-0">
                    </div>
                </div>
                <div class="col-12 py-3">
                    <p class="text-body fs-sm text-sm-end text-md-start mb-sm-0 mb-md-3 ms-0 ms-sm-auto ms-md-0 me-4">Уважаемые посетители!
                        Данный сайт является каталогом товаров питомника растений и не является интернет-магазином.
                        Размещенная информация (включая цены, характеристики, изображения) представлена для ознакомления и не считается офертой (ст. 407 Гражданского кодекса Республики Беларусь).
                        Для оформления заявки и уточнения актуальных условий просим связываться с нашими менеджерами.</p>
                </div>
            </div>
        </div>

        <!-- Copyright + Payment methods -->
        <div class="d-md-flex align-items-center border-top py-4">
            <p class="text-body fs-xs text-center text-md-start mb-0 me-4 order-md-1">&copy; 2021-{{ date('Y') }} Все права защищены. Разработка сайта <span class="animate-underline"><a class="animate-target text-dark-emphasis fw-medium text-decoration-none" href="https://webart.by/" target="_blank" rel="noreferrer">WebArt.BY</a></span></p>
            <div class="ms-md-auto order-md-2 mt-3 mt-md-0">
                <a class="nav-link fs-xs fw-normal p-0" href="{{ route('privacy-policy') }}">Политика конфиденциальности</a>
            </div>
        </div>
    </div>
</footer>
