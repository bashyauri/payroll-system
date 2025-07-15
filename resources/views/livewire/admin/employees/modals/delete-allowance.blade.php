<flux:modal :name="'delete-allowance-' . $allowance->id" class="max-w-md" close-on-click-away close-on-escape>
    <div class="p-6">
        <!-- Modal header with icon and title -->
        <div class="flex items-start gap-3 mb-4">
            <div
                class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/50">
                <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>

            <div>
                <flux:heading size="lg" class="text-gray-900 dark:text-white">
                    Confirm Allowance Deletion
                </flux:heading>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    This action cannot be undone
                </p>
            </div>
        </div>

        <!-- Modal content -->
        <div class="mt-4 space-y-2">
            <p class="text-gray-700 dark:text-gray-300">
                You're about to delete the <span
                    class="font-semibold">{{ $allowanceToDelete->type->name ?? 'N/A' }}</span> deduction for:
            </p>

            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg mt-2">
                @if($allowanceToDelete?->employee?->user?->avatar_url)
                    <img class="h-10 w-10 rounded-full" src="{{ $allowanceToDelete?->employee->user->avatar_url }}" alt="">
                @else
                    <div
                        class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                        <span class="text-gray-500 dark:text-gray-400 text-sm font-medium">
                            {{ substr($allowanceToDelete?->employee?->user?->name ?? 'N/A', 0, 1) }}
                        </span>
                    </div>
                @endif

                <div>
                    <p class="font-medium text-gray-900 dark:text-white">
                        {{ $allowanceToDelete?->employee?->user?->name ?? 'N/A' }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $allowanceToDelete?->employee?->staff_id ?? 'N/A' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg mt-3">
                <svg class="h-5 w-5 text-red-500 dark:text-red-400" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="text-sm text-red-600 dark:text-red-400">
                    This will permanently remove the allowance record.
                </p>
            </div>
        </div>

        <!-- Action buttons -->
        <div class="flex justify-end gap-3 pt-6">
            <flux:button variant="ghost"
                @click="$dispatch('close-modal', { name: 'delete-allowance-' + {{ $allowanceToDelete->id ?? 0 }} })"
                wire:loading.attr="disabled">
                Cancel
            </flux:button>

            <flux:button variant="danger" wire:click="deleteAllowance" wire:loading.attr="disabled"
                wire:target="deleteAllowance" class="flex items-center gap-2">
                <span wire:loading.remove wire:target="deleteAllowance">
                    Confirm Delete
                </span>
                <span wire:loading wire:target="deleteAllowance">
                    Deleting...
                </span>
                <svg wire:loading wire:target="deleteDeduction" class="animate-spin h-4 w-4"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
            </flux:button>
        </div>
    </div>
</flux:modal>