<!-- Page footer -->
<footer class="footer position-relative bg-dark border-top">
    <span class="position-absolute top-0 start-0 w-100 h-100 bg-body d-none d-block-dark"></span>
    <div class="container position-relative z-1 pt-sm-2 pt-md-3 pt-lg-4" data-bs-theme="dark">

        <!-- Columns with links that are turned into accordion on screens < 500px wide (sm breakpoint) -->
        <div class="accordion py-5" id="footerLinks">
            <div class="row gy-4">
                <div class="col-12 col-lg-5">
                    <div class="footer-section h-100">
                        <h4 class="footer-section-title mb-3">
                            <span class="text-dark-emphasis text-decoration-none">Наши контакты</span>
                        </h4>

                        @if(!empty($siteSettings['phones']['value']))
                            @foreach($siteSettings['phones']['value'] as $phone)
                                <p class="footer-text text-body fs-md mb-2">
                                    <a class="footer-contact-link nav-link animate-target text-white fw-semibold p-0" href="tel:{{ preg_replace('/[^\d+]/', '', $phone['number'] ?? '') }}">
                                        <i class="ci-phone fs-md"></i>
                                        <span>{{ $phone['number'] ?? '' }}</span>
                                    </a>
                                </p>
                            @endforeach
                        @else
                            {{-- Fallback на хардкод если настройки не заполнены --}}
                            <p class="footer-text text-body fs-md mb-2">
                                <a class="footer-contact-link nav-link animate-target text-white fw-semibold p-0" href="tel:+375296403709">
                                    <i class="ci-phone fs-md"></i>
                                    <span>+375 (29) 640-37-09</span>
                                </a>
                            </p>
                            <p class="footer-text text-body fs-md mb-2">
                                <a class="footer-contact-link nav-link animate-target text-white fw-semibold p-0" href="tel:+375445913335">
                                    <i class="ci-phone fs-md"></i>
                                    <span>+375 (44) 591-33-35</span>
                                </a>
                            </p>
                            <p class="footer-text text-body fs-md mb-2">
                                <a class="footer-contact-link nav-link animate-target text-white fw-semibold p-0" href="tel:+375295913372">
                                    <i class="ci-phone fs-md"></i>
                                    <span>+375 (29) 591-33-72</span>
                                </a>
                            </p>
                        @endif

                        <p class="footer-text text-body fs-md mb-2">
                            <a class="footer-contact-link nav-link animate-target text-white fw-semibold p-0" href="mailto:{{ $siteSettings['email'] ?? 'xsv.by@yandex.by' }}">
                                <i class="ci-mail fs-md"></i>
                                <span>{{ $siteSettings['email'] ?? 'xsv.by@yandex.by' }}</span>
                            </a>
                        </p>

                        <p class="footer-text text-body fs-md mb-0">

                                <i class="ci-map-pin fs-md"></i>
                                <span>{!! nl2br(e($siteSettings['address'] ?? "Минская обл., Логойский р-н\n д. Зелёный сад, ул. Подлесная, 20")) !!}</span>

                        </p>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="footer-section h-100">
                        <h4 class="footer-section-title mb-3">
                            <span class="text-dark-emphasis text-decoration-none">Наши реквизиты</span>
                        </h4>

                        @if(!empty($siteSettings['business_info']['value']))
                            @php
                                $business = $siteSettings['business_info']['value'];
                            @endphp
                            <p class="footer-text text-body fs-md mb-2">{{ $business['company_name'] ?? 'ООО "Сказочный сад"' }}</p>
                            <p class="footer-text text-body fs-sm mb-2">
                                УНП: {{ $business['unp'] ?? '690876969' }}<br>
                                {{ $business['additional_info'] ?? 'Свидетельство о регистрации выдано Логойским райисполком от 24.12.2025 г.' }}
                            </p>
                            @if(!empty($business['bank_account']))
                                <p class="footer-text text-body fs-sm mb-0">
                                    Р/сч: {{ $business['bank_account'] }}<br>
                                    в {{ $business['bank_name'] ?? 'ЗАО БСБ Банк' }} {{ $business['bank_address'] ?? '220004, г. Минск, пр. Победителей, 23, корп. 3' }}<br>
                                    код {{ $business['bank_code'] ?? 'UNBSBY2X' }}
                                </p>
                            @endif
                        @else
                            {{-- Fallback на хардкод если настройки не заполнены --}}
                            <p class="footer-text text-body fs-md mb-2">ООО "Сказочный сад"</p>
                            <p class="footer-text text-body fs-sm mb-2">
                                УНП: 690876969<br>
                                Свидетельство о регистрации<br>
                                выдано Логойским райисполком от 24.12.2025 г.
                            </p>
                            <p class="footer-text text-body fs-sm mb-0">
                                Р/сч: BY85 UNBS 3012 2578 3000 0000 0933<br>
                                в ЗАО БСБ Банк 220004, г. Минск, пр. Победителей, 23, корп. 3<br>
                                код UNBSBY2X
                            </p>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-lg-3">
                    <div class="footer-section h-100">
                        <div class="accordion-item border-0 bg-transparent">
                            <h6 class="accordion-header" id="companyHeading">
                                <span class="text-dark-emphasis d-none d-sm-block">Разделы каталога</span>
                                <button type="button" class="accordion-button collapsed py-3 d-sm-none" data-bs-toggle="collapse" data-bs-target="#companyLinks" aria-expanded="false" aria-controls="companyLinks">Разделы каталога</button>
                            </h6>
                            <div class="accordion-collapse collapse show d-sm-block" id="companyLinks" aria-labelledby="companyHeading" data-bs-parent="#footerLinks">
                                <ul class="nav flex-column gap-2 pt-sm-3 pb-3 mt-n1 mb-1">
                                    @foreach($footerCategories as $category)
                                        <li class="d-flex w-100 pt-1">
                                            <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0"
                                               href="{{ route('catalog.show', $category->getFullPath()) }}">
                                                {{ $category->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <hr class="d-sm-none my-0">
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="row g-3 align-items-stretch">
                        <div class="col-12 col-md-6">
                            <a class="footer-route-link nav btn btn-outline-warning rounded-pill px-3 justify-content-center h-100"
                               href="https://yandex.ru/maps/?rtext=~54.171193, 27.774528"
                               target="_blank"
                               rel="noreferrer">
                                <i class="ci-send fs-lg me-2" style="margin-top: .1875rem"></i>
                                <span class="animate-target">Построить маршрут в Яндекс.Карты</span>
                                <svg width="9" height="18" viewBox="0 0 9 18" fill="none" xmlns="http://www.w3.org/2000/svg" class="ms-2">
                                    <path d="M8.20626 0H5.69812C3.15705 0 0.592979 1.91327 0.592979 6.18771C0.592979 8.40178 1.51326 10.1262 3.20025 11.1068L0.112583 16.8054C-0.0338148 17.0749 -0.0376421 17.3803 0.102308 17.6224C0.238949 17.8588 0.488818 18 0.770442 18H2.33238C2.6872 18 2.96386 17.8251 3.09654 17.5188L5.99157 11.745H6.20285V17.2804C6.20285 17.6705 6.52568 18 6.90781 18H8.27229C8.70082 18 9 17.6949 9 17.2581V0.787605C9.00007 0.323895 8.67369 0 8.20626 0ZM6.20285 9.18021H5.83014C4.38495 9.18021 3.52218 7.97737 3.52218 5.96261C3.52218 3.45741 4.61206 2.56482 5.63209 2.56482H6.20285V9.18021Z" fill="#D7143A" />
                                </svg>
                            </a>
                        </div>
                        <div class="col-12 col-md-6">
                            <a class="footer-route-link nav btn btn-outline-success rounded-pill px-3 justify-content-center h-100"
                               href="https://www.google.com/maps?saddr=My+Location&daddr=54.171193, 27.774528"
                               target="_blank"
                               rel="noreferrer">
                                <i class="ci-send fs-lg me-2"></i>
                                <span class="animate-target">Построить маршрут в Google Maps</span>
                                <i class="ci-google fs-lg ms-2" style="margin-top: .1875rem"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <p class="footer-disclaimer text-body fs-sm mb-0">Уважаемые посетители! Данный сайт является каталогом товаров питомника растений и не является интернет-магазином. Размещенная информация (включая цены, характеристики, изображения) представлена для ознакомления и не считается офертой (ст. 407 Гражданского кодекса Республики Беларусь). Для оформления заявки и уточнения актуальных условий просим связываться с нашими менеджерами.</p>
                </div>
            </div>
        </div>

        <!-- Copyright + Payment methods -->
        <div class="d-md-flex align-items-center border-top py-4">
            <p class="text-body fs-xs text-center text-md-start mb-0 me-4 order-md-1">&copy; 2021-{{ date('Y') }} Все права защищены. Разработка сайта <span class="animate-underline"><a class="animate-target text-dark-emphasis fw-medium text-decoration-none" href="https://webart.by/" target="_blank" rel="noreferrer">WebArt.BY</a></span></p>
            <div class="ms-md-auto order-md-2 mt-3 mt-md-0">
                <a class="nav-link fs-xs fw-normal p-0 text-center text-md-start" href="{{ route('privacy-policy') }}">Политика конфиденциальности</a>
            </div>
        </div>
    </div>
</footer>
