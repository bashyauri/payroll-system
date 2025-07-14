<flux:modal :name="'delete-employee-'.$employee->id" class="max-w-md">
    <div class="p-6">
        <!-- Modal header with unique close -->
        <div class="flex items-center justify-between mb-4">
            <flux:heading size="lg">Confirm Deletion</flux:heading>
            <button @click="$dispatch('close-modal', { name: 'delete-employee-'.$employeeToDelete->id })"
                class="text-gray-400 hover:text-gray-500">
                ✕
            </button>
        </div>

        <!-- Modal content -->
        <p class="text-gray-600 dark:text-gray-400">
            Delete user <span class="font-semibold">{{ $employeeToDelete->name ?? '' }}</span>?
        </p>

        <!-- Action buttons with proper closing -->
        <div class="flex justify-end gap-3 pt-4">
            <flux:button variant="ghost"
                @click="$dispatch('close-modal', { name: 'delete-employee-'.$employeeToDelete->id })">
                Cancel
            </flux:button>
            <flux:button variant="danger" wire:click="deleteEmployee"
                @click="$dispatch('close-modal', { name: 'delete-employee-'.$employeeToDelete->id })">
                Confirm Delete
            </flux:button>
        </div>
    </div>
</flux:modal>