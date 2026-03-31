<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\ProductResource\Pages;

use App\Models\Product;
use App\MoonShine\Resources\CategoryResource\CategoryResource;
use App\MoonShine\Resources\CountryResource\CountryResource;
use App\MoonShine\Resources\ProductAttributeValue\ProductAttributeValueResource;
use App\MoonShine\Resources\ProductAttributeValue\Support\HasProductAttributeFields;
use App\MoonShine\Resources\ProductResource\ProductResource;
use App\MoonShine\Resources\SupplierResource\SupplierResource;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Laravel\Fields\Relationships\RelationRepeater;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Preview;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

/**
 * @extends FormPage<ProductResource, Product>
 */
final class ProductFormPage extends FormPage
{
    use HasProductAttributeFields;

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make([
                Tabs::make([
                    Tab::make('Основное', [
                        ID::make(),

                        Text::make('Название', 'name')
                            ->when(
                                fn () => $this->getResource()->isCreateFormPage(),
                                fn (Text $field) => $field->reactive(),
                                fn (Text $field) => $field
                            )
                            ->required(),

                        Slug::make('Slug', 'slug')
                            ->unique()
                            ->locked()
                            ->when(
                                fn () => $this->getResource()->isCreateFormPage(),
                                fn (Slug $field) => $field->from('name')->live(),
                                fn (Slug $field) => $field->readonly()
                            ),

                        Flex::make([
                            BelongsTo::make(
                                'Категория',
                                'category',
                                resource: CategoryResource::class,
                            )
                                ->nullable()
                                ->searchable()
                                ->valuesQuery(
                                    static fn (Builder $q) => $q->active()->select(['id', 'name'])
                                ),

                            BelongsTo::make(
                                'Поставщик',
                                'supplier',
                                resource: SupplierResource::class,
                            )
                                ->nullable()
                                ->searchable()
                                ->valuesQuery(
                                    static fn (Builder $q) => $q->active()->select(['id', 'name'])
                                ),

                            BelongsTo::make(
                                'Страна',
                                'country',
                                resource: CountryResource::class,
                            )
                                ->nullable()
                                ->searchable()
                                ->valuesQuery(
                                    static fn (Builder $q) => $q->active()->select(['id', 'name'])
                                ),
                        ]),

                        BelongsToMany::make(
                            'Дополнительные категории',
                            'categories',
                            resource: CategoryResource::class,
                        )
                            ->selectMode()
                            ->searchable()
                            ->valuesQuery(
                                static fn (Builder $q) => $q->active()->select(['id', 'name'])
                            ),

                        Text::make('Артикул', 'sku'),

                        Flex::make([
                            Number::make('Цена', 'price')
                                ->min(0)
                                ->step(0.01)
                                ->default(0),

                            Number::make('Старая цена', 'old_price')
                                ->nullable()
                                ->min(0)
                                ->step(0.01),

                            Number::make('Оптовая цена', 'wholesale_price')
                                ->nullable()
                                ->min(0)
                                ->step(0.01),

                            Number::make('Мин. кол-во для опта', 'wholesale_min_quantity')
                                ->nullable()
                                ->min(0),
                        ]),

                        Flex::make([
                            Number::make('Остаток', 'stock')
                                ->min(0)
                                ->default(0),

                            Number::make('Сортировка', 'sort_order')
                                ->min(0)
                                ->default(0),
                        ]),

                        Flex::make([
                            Switcher::make('Активен', 'is_active')
                                ->default(true),

                            Switcher::make('Хит', 'is_featured'),

                            Switcher::make('Новинка', 'is_new'),

                            Switcher::make('Бестселлер', 'is_bestseller'),

                            Switcher::make('В наличии', 'in_stock')
                                ->default(true),
                        ]),
                    ]),

                    Tab::make('Контент', [
                        Image::make('Изображение', 'image')
                            ->disk(moonshineConfig()->getDisk())
                            ->keepOriginalFileName()
                            ->dir('products')
                            ->allowedExtensions(['jpg', 'png', 'jpeg', 'gif', 'webp'])
                            ->removable(),

                        Image::make('Галерея', 'gallery')
                            ->multiple()
                            ->disk(moonshineConfig()->getDisk())
                            ->keepOriginalFileName()
                            ->dir('products')
                            ->allowedExtensions(['jpg', 'png', 'jpeg', 'gif', 'webp'])
                            ->removable(),

                        Textarea::make('Краткое описание', 'short_description'),

                        TinyMce::make('Описание', 'description'),
                    ]),

                    Tab::make('Атрибуты', [
                        RelationRepeater::make(
                            'Атрибуты',
                            'productAttributeValues',
                            resource: ProductAttributeValueResource::class
                        )
                            ->fields([
                                $this->makeProductAttributeField(true),
                                $this->makeProductAttributeValueField(true),
                            ])
                            ->creatable()
                            ->removable(),
                        $this->makeProductAttributeDependencyScript(),
                    ]),

                    Tab::make('SEO', [
                        Box::make('Мета-теги', [
                            Text::make('SEO Title', 'seo_title'),

                            Text::make('SEO H1', 'seo_h1'),

                            Textarea::make('SEO Description', 'seo_description'),

                            Text::make('Старый URL', 'legacy_url')
                                ->nullable()
                                ->placeholder('/katalog/listvennyie-rasteniya/.../tovar.html'),
                        ]),
                    ]),
                ]),
            ]),
        ];
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [
            'name' => 'required',
            'category_id' => 'nullable',
            'categories' => 'nullable|array',
            'categories.*' => 'integer|exists:categories,id',
            'supplier_id' => 'nullable',
            'country_id' => 'nullable',
            'legacy_url' => 'nullable|string|max:255|unique:products,legacy_url,' . $item->getKey(),
            'sku' => 'nullable',
            'price' => 'nullable',
            'old_price' => 'nullable',
            'wholesale_price' => 'nullable',
            'wholesale_min_quantity' => 'nullable',
            'stock' => 'nullable',
            'is_active' => 'nullable',
            'is_featured' => 'nullable',
            'is_new' => 'nullable',
            'is_bestseller' => 'nullable',
            'in_stock' => 'nullable',
            'sort_order' => 'nullable',
            'seo_title' => 'nullable',
            'seo_h1' => 'nullable',
            'seo_description' => 'nullable',
        ];
    }

    protected function buttons(): ListOf
    {
        $buttons = new ListOf(ActionButtonContract::class, [
            ActionButton::make(
                'Открыть на сайте',
                static fn (Product $product): string => route('products.show', $product->slug),
            )
                ->info()
                ->icon('globe-alt')
                ->blank()
                ->customAttributes(['rel' => 'noopener noreferrer']),

            ActionButton::make('Создать новый товар', $this->getResource()->getFormPageUrl())
                ->primary()
                ->icon('plus'),

            $this->modifyDeleteButton(
                $this->getResource()->getDeleteButton(
                    redirectAfterDelete: $this->getResource()->getRedirectAfterDelete(),
                    isAsync: false,
                )
            ),
        ]);

        if (! $this->isItemExists() || ! $this->getItem() instanceof Product) {
            return $buttons->except(
                static fn (ActionButtonContract $button): bool => $button->getLabel() === 'Открыть на сайте'
            );
        }

        return $buttons;
    }

    protected function makeProductAttributeDependencyScript(): Preview
    {
        return Preview::make('', '_product_attribute_dependency_script')
            ->changeFill(static fn (): string => <<<'HTML'
<script>
(() => {
    if (window.__productAttributeDependencyInitialized) {
        return;
    }

    window.__productAttributeDependencyInitialized = true;

    const attributeSelector = 'select[data-product-attribute-field]';
    const valueSelector = 'select[data-product-attribute-value-field]';
    const currentAttributeInput = 'product_attribute_selected_attribute';

    const ensureHiddenInput = (form, name) => {
        let input = form.querySelector(`input[name="${name}"]`);

        if (!input) {
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            form.append(input);
        }

        return input;
    };

    const getRow = (element) => element.closest('tr');

    const getValueSelectByRow = (row) => row ? row.querySelector(valueSelector) : null;

    const getAttributeSelectByRow = (row) => row ? row.querySelector(attributeSelector) : null;

    const syncCurrentAttribute = (valueSelect) => {
        if (!(valueSelect instanceof HTMLSelectElement) || !valueSelect.form) {
            return;
        }

        const attributeSelect = getAttributeSelectByRow(getRow(valueSelect));

        ensureHiddenInput(valueSelect.form, currentAttributeInput).value = attributeSelect?.value || '';
    };

    const resolveValueSelect = (target) => {
        if (!(target instanceof Element)) {
            return null;
        }

        const row = getRow(target);

        if (!row) {
            return null;
        }

        return Array.from(row.querySelectorAll(valueSelector)).find((select) => {
            if (!(select instanceof HTMLSelectElement)) {
                return false;
            }

            const wrapper = select.tomselect?.wrapper;

            return wrapper ? wrapper.contains(target) : select === target || select.contains(target);
        }) || null;
    };

    document.addEventListener('change', (event) => {
        const target = event.target;

        if (!(target instanceof HTMLSelectElement) || !target.matches(attributeSelector)) {
            return;
        }

        const valueSelect = getValueSelectByRow(getRow(target));

        if (!(valueSelect instanceof HTMLSelectElement)) {
            return;
        }

        if (valueSelect.tomselect) {
            valueSelect.tomselect.clear(true);
            valueSelect.tomselect.clearOptions();
            syncCurrentAttribute(valueSelect);
            valueSelect.tomselect.load('*');
        } else {
            valueSelect.value = '';
            syncCurrentAttribute(valueSelect);
        }
    });

    ['focusin', 'mousedown'].forEach((eventName) => {
        document.addEventListener(eventName, (event) => {
            const valueSelect = resolveValueSelect(event.target);

            if (valueSelect) {
                syncCurrentAttribute(valueSelect);

                if (valueSelect.tomselect) {
                    valueSelect.tomselect.clearOptions();
                    valueSelect.tomselect.load('*');
                }
            }
        });
    });
})();
</script>
HTML)
            ->withoutWrapper();
    }
}
