<flux:modal :name="'edit-employee-'.$employee?->id" class="md:w-[800px]"
    overlay-class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
    wrapper-class="fixed inset-0 overflow-y-auto flex items-center justify-center min-h-screen p-4"
    dialog-class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl transition-all w-full max-w-2xl">
    <div class="p-6 space-y-6">
        <!-- Header Section -->
        <div class="flex items-start justify-between">
            <div>
                <flux:heading size="lg">Edit User Profile</flux:heading>
                <flux:text class="text-gray-600 dark:text-gray-400 mt-1">
                    Update employee details for {{ $employee?->user->name }}
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
            on="employee-updated">
            {{ __('Employee updated successfully!') }}
        </x-action-message>

        <!-- Form Section -->
        <form wire:submit.prevent="updateEmployee">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-4">
                    <input type="hidden" wire:model.defer="staff_id">
                    <flux:select label="Department" wire:model.defer="department_id" class="w-full"
                        icon="office-building" required>
                        <option value="">Select Department</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </flux:select>
                    @error('department_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                    <flux:select label="Position" wire:model.defer="position_id" class="w-full" icon="briefcase">
                        <option value="">Select Position</option>
                        @foreach ($positions as $position)
                            <option value="{{ $position->id }}">{{ $position->title }}</option>
                        @endforeach
                    </flux:select>
                    @error('position_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Right Column -->
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:input type="number" label="Level" wire:model.defer="level" placeholder="e.g. L4"
                                class="w-full" />
                            @error('level') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <flux:input type="number" label="Step" wire:model.defer="step" placeholder="e.g. S2"
                                class="w-full" />
                            @error('step') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bank Details Section -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <flux:heading size="md" class="mb-4">Bank Information</flux:heading>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <flux:select label="Bank" wire:model.defer="bank_id" class="w-full" icon="briefcase">
                            <option value="">Select Bank</option>
                            @foreach ($banks as $bank)
                                <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                            @endforeach
                        </flux:select>
                        @error('bank_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <flux:input label="Account Number" wire:model.defer="account_number" placeholder="1234567890"
                            class="w-full" icon="credit-card" />
                        @error('account_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
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
                        <span wire:loading.remove wire:target="updateEmployee">
                            Save Changes
                        </span>
                    </flux:button>
                </div>
            </div>
        </form>
    </div>
</flux:modal>