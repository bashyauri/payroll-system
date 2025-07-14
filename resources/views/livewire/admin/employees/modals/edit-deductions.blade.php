<flux:modal :name="'edit-deduction-'.$deduction?->id" class="md:w-[800px]"
    overlay-class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
    wrapper-class="fixed inset-0 overflow-y-auto flex items-center justify-center min-h-screen p-4"
    dialog-class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl transition-all w-full max-w-2xl">
    <div class="p-6 space-y-6">
        <!-- Header Section -->
        <div class="flex items-start justify-between">
            <div>
                <flux:heading size="lg">Edit User Profile</flux:heading>
                <flux:text class="text-gray-600 dark:text-gray-400 mt-1">
                    Update deduction for {{ $deduction->employee?->user->name }}
                </flux:text>
            </div>
        </div>

        <!-- Error Messages -->
        @if($errors->any())
            <div class="bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-200 px-4 py-3 rounded-md">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Success Message -->
        <x-action-message
            class="bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-200 px-4 py-3 rounded-md"
            on="deduction-updated">
            {{ __('Deduction updated successfully!') }}
        </x-action-message>

        <!-- Form Section -->
        <form wire:submit.prevent="updateDeduction">
            <div class="space-y-4">
                <!-- Employee Display -->
                @if($selectedEmployee)
                    <p class="text-gray-600 text-sm">
                        Editing deduction for <strong>{{ $selectedEmployee['name'] ?? '' }}</strong>
                        ({{ $selectedEmployee['staff_id'] ?? '' }})
                    </p>
                @endif

                <!-- Deduction Type -->
                <flux:select label="Deduction Type" wire:model.defer="deduction_type_id" required>
                    <option value="">Select Deduction Type</option>
                    @foreach ($deductionTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </flux:select>
                @error('deduction_type_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                <!-- Amount -->
                <flux:input type="number" label="Amount" wire:model.defer="amount" placeholder="e.g. 5000" />
                @error('amount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                <!-- Note -->
                <flux:input type="text" label="Note" wire:model.defer="note" placeholder="Optional note" />
                @error('note') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                <flux:button variant="ghost" class="cursor-pointer" type="button" @click="$wire.closeForm()">
                    Cancel
                </flux:button>

                <div class="flex gap-3">
                    <flux:button variant="outline" type="button" @click="$wire.resetForm()">
                        Reset
                    </flux:button>
                    <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                        <span wire:loading wire:target="updateEmployee">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Saving...
                        </span>
                        <span wire:loading.remove wire:target="updateDeduction">
                            Save Changes
                        </span>
                    </flux:button>
                </div>
            </div>
        </form>

    </div>
</flux:modal>