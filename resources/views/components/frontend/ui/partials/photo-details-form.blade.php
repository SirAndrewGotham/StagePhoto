<div class="border-t border-gray-200 dark:border-gray-700 p-6">
    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">@lang('photo_details')</h3>

    <div class="space-y-3">
        <input type="text"
               wire:model="photoTitle"
               placeholder="@lang('photo_title_optional')"
               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-stage-500 dark:bg-gray-700">
        <textarea wire:model="photoDescription"
                  placeholder="@lang('photo_description_optional')"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-stage-500 dark:bg-gray-700"></textarea>
    </div>
</div>
