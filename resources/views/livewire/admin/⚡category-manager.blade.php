<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Services\CategoryService;

new class extends Component {
    use WithPagination;

    public $editingCategory;
    public $form = [
        'slug' => '',
        'icon' => '',
        'type' => 'music',
        'sort_order' => 0,
        'is_active' => true,
        'translations' => [
            'ru' => ['name' => '', 'description' => ''],
            'en' => ['name' => '', 'description' => ''],
            'eo' => ['name' => '', 'description' => ''],
        ],
    ];

    protected $categoryService;

    public function boot(CategoryService $categoryService): void
    {
        $this->categoryService = $categoryService;
    }

    protected function rules(): array
    {
        return [
            'form.slug' => 'required|string|unique:categories,slug' . ($this->editingCategory ? ',' . $this->editingCategory->id : ''),
            'form.icon' => 'nullable|string|max:10',
            'form.type' => 'required|in:music,theater,dance,other',
            'form.sort_order' => 'integer',
            'form.is_active' => 'boolean',
            'form.translations.ru.name' => 'required|string|max:255',
            'form.translations.en.name' => 'required|string|max:255',
            'form.translations.eo.name' => 'required|string|max:255',
            'form.translations.ru.description' => 'nullable|string',
            'form.translations.en.description' => 'nullable|string',
            'form.translations.eo.description' => 'nullable|string',
        ];
    }

    public function create(): void
    {
        $this->reset('form', 'editingCategory');
        $this->dispatch('open-modal', 'category-form');
    }

    public function edit($id): void
    {
        $this->editingCategory = Category::with('translations')->findOrFail($id);

        $this->form = [
            'slug' => $this->editingCategory->slug,
            'icon' => $this->editingCategory->icon,
            'type' => $this->editingCategory->type,
            'sort_order' => $this->editingCategory->sort_order,
            'is_active' => $this->editingCategory->is_active,
            'translations' => [
                'ru' => ['name' => '', 'description' => ''],
                'en' => ['name' => '', 'description' => ''],
                'eo' => ['name' => '', 'description' => ''],
            ],
        ];

        foreach ($this->editingCategory->translations as $translation) {
            $this->form['translations'][$translation->locale] = [
                'name' => $translation->name,
                'description' => $translation->description,
            ];
        }

        $this->dispatch('open-modal', 'category-form');
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingCategory) {
            $this->editingCategory->update([
                'slug' => $this->form['slug'],
                'icon' => $this->form['icon'],
                'type' => $this->form['type'],
                'sort_order' => $this->form['sort_order'],
                'is_active' => $this->form['is_active'],
            ]);

            foreach ($this->form['translations'] as $locale => $data) {
                CategoryTranslation::updateOrCreate(
                    ['category_id' => $this->editingCategory->id, 'locale' => $locale],
                    ['name' => $data['name'], 'description' => $data['description']]
                );
            }

            session()->flash('message', 'Category updated successfully!');
        } else {
            $category = Category::create([
                'slug' => $this->form['slug'],
                'icon' => $this->form['icon'],
                'type' => $this->form['type'],
                'sort_order' => $this->form['sort_order'],
                'is_active' => $this->form['is_active'],
            ]);

            foreach ($this->form['translations'] as $locale => $data) {
                CategoryTranslation::create([
                    'category_id' => $category->id,
                    'locale' => $locale,
                    'name' => $data['name'],
                    'description' => $data['description'],
                ]);
            }

            session()->flash('message', 'Category created successfully!');
        }

        $this->categoryService->clearCache();
        $this->dispatch('close-modal', 'category-form');
        $this->reset('form', 'editingCategory');
    }

    public function delete($id): void
    {
        $category = Category::findOrFail($id);
        $category->delete();
        $this->categoryService->clearCache();
        session()->flash('message', 'Category deleted successfully!');
    }

    public function render(): string
    {
        Category::with('translations')
            ->ordered()
            ->paginate(15);

        return <<<'HTML'
            <div>
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Manage Categories</h2>
                    <button wire:click="create" class="px-4 py-2 bg-stage-600 text-white rounded-lg hover:bg-stage-700">
                        + Add Category
                    </button>
                </div>

                @if(session()->has('message'))
                    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg">
                        {{ session('message') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white dark:bg-gray-800 rounded-lg overflow-hidden">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left">Icon</th>
                                <th class="px-4 py-3 text-left">Slug</th>
                                <th class="px-4 py-3 text-left">Name (RU)</th>
                                <th class="px-4 py-3 text-left">Type</th>
                                <th class="px-4 py-3 text-left">Sort Order</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <td class="px-4 py-3">{{ $category->icon }}</td>
                                    <td class="px-4 py-3">{{ $category->slug }}</td>
                                    <td class="px-4 py-3">{{ $category->translations->firstWhere('locale', 'ru')?->name }}</td>
                                    <td class="px-4 py-3">{{ $category->type }}</td>
                                    <td class="px-4 py-3">{{ $category->sort_order }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $category->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <button wire:click="edit({{ $category->id }})" class="text-blue-600 hover:text-blue-800 mr-2">Edit</button>
                                        <button wire:click="delete({{ $category->id }})" wire:confirm="Are you sure?" class="text-red-600 hover:text-red-800">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $categories->links() }}
                </div>

                <!-- Modal Form -->
                <div x-data="{ open: false }"
                     x-on:open-modal.window="if($event.detail.id === 'category-form') open = true"
                     x-on:close-modal.window="if($event.detail.id === 'category-form') open = false"
                     x-show="open"
                     x-cloak
                     class="fixed inset-0 z-50 overflow-y-auto"
                     style="display: none;">
                    <div class="flex items-center justify-center min-h-screen px-4">
                        <div class="fixed inset-0 bg-black opacity-50" @click="open = false"></div>

                        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full p-6">
                            <h3 class="text-xl font-bold mb-4">
                                {{ $editingCategory ? 'Edit Category' : 'Create Category' }}
                            </h3>

                            <form wire:submit="save">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Slug</label>
                                        <input type="text" wire:model="form.slug" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700">
                                        @error('form.slug') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-1">Icon (emoji)</label>
                                        <input type="text" wire:model="form.icon" placeholder="🎸" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-1">Type</label>
                                        <select wire:model="form.type" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700">
                                            <option value="music">Music</option>
                                            <option value="theater">Theater</option>
                                            <option value="dance">Dance</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-1">Sort Order</label>
                                        <input type="number" wire:model="form.sort_order" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700">
                                    </div>

                                    <div>
                                        <label class="flex items-center">
                                            <input type="checkbox" wire:model="form.is_active" class="mr-2">
                                            <span class="text-sm font-medium">Active</span>
                                        </label>
                                    </div>

                                    <div class="border-t pt-4">
                                        <h4 class="font-semibold mb-3">Translations</h4>

                                        @foreach(['ru', 'en', 'eo'] as $locale)
                                            <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-700 rounded">
                                                <h5 class="font-medium mb-2">{{ strtoupper($locale) }}</h5>
                                                <div class="space-y-2">
                                                    <input type="text" wire:model="form.translations.{{ $locale }}.name"
                                                           placeholder="Name" class="w-full px-3 py-2 border rounded-lg">
                                                    <textarea wire:model="form.translations.{{ $locale }}.description"
                                                              placeholder="Description (optional)" rows="2" class="w-full px-3 py-2 border rounded-lg"></textarea>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="flex justify-end gap-2 mt-6">
                                    <button type="button" @click="open = false" class="px-4 py-2 border rounded-lg">Cancel</button>
                                    <button type="submit" class="px-4 py-2 bg-stage-600 text-white rounded-lg">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        HTML;
    }
};
?>
