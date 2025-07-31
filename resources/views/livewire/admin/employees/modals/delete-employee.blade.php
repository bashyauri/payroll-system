<flux:modal :name="'delete-employee-' . $employee->id" class="max-w-md">
    <div class="p-6">
        <!-- Modal header -->
        <div class="flex items-center justify-between mb-4">
            <flux:heading size="lg">Confirm Deletion</flux:heading>
        </div>

        <!-- Modal content -->
        <p class="text-gray-600 dark:text-gray-400">
            Delete user <span class="font-semibold">{{ $employeeToDelete->user->name ?? '' }}</span>?
        </p>

        <!-- Action buttons -->
        <div class="flex justify-end gap-3 pt-4">
            <flux:button variant="ghost"
                @click="$dispatch('close-modal', { name: 'delete-employee-{{ $employeeToDelete->id ?? 0 }}' })">
                Cancel
            </flux:button>
            <flux:button variant="danger" wire:click="deleteEmployee"
                @click="$dispatch('close-modal', { name: 'delete-employee-{{ $employeeToDelete->id ?? 0 }}' })">
                Confirm Delete
            </flux:button>
        </div>
    </div>
</flux:modal>