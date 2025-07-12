<flux:modal :name="'edit-user-'.$user?->id" class="md:w-[800px]"
    overlay-class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
    wrapper-class="fixed inset-0 overflow-y-auto flex items-center justify-center min-h-screen p-4"
    dialog-class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl transition-all w-full max-w-2xl">
    <div class="p-6 space-y-6">
        <!-- Header Section -->
        <div class="flex items-start justify-between">
            <div>
                <flux:heading size="lg">Edit User Profile</flux:heading>
                <flux:text class="text-gray-600 dark:text-gray-400 mt-1">
                    Update account details and permissions for {{ $user?->name }}
                </flux:text>
            </div>

        </div>

        <!-- Success Message -->
        <x-action-message
            class="bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-200 px-4 py-3 rounded-md"
            on="user-updated">
            {{ __('User updated successfully!') }}
        </x-action-message>

        <!-- Form Section -->
        <form wire:submit.prevent="updateUser">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-4">
                    <flux:input label="Full Name" wire:model.defer="name" placeholder="John Doe" icon="user" required />

                    <flux:input label="Email" wire:model.defer="email" type="email" placeholder="john@example.com"
                        icon="envelope" required />

                    <flux:select label="Status" wire:model.defer="status" icon="status" required>
                        <option value="">Select status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </flux:select>
                </div>

                <!-- Right Column -->
                <div class="space-y-4">
                    <flux:input label="Phone Number" wire:model.defer="phone" type="tel" placeholder="+1 (555) 123-4567"
                        icon="phone" />

                    <flux:select label="Department" wire:model.defer="department_id" icon="building">
                        <option value="">Select Department</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select label="Role" wire:model.defer="role_id" icon="badge" required>
                        <option value="">Select role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </flux:select>
                </div>
            </div>

            <!-- Password Update Section (Conditional) -->
            {{-- <div x-data="{ showPasswordFields: false }"
                class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                <button type="button" @click="showPasswordFields = !showPasswordFields"
                    class="flex items-center text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span x-text="showPasswordFields ? 'Hide Password Fields' : 'Change Password'"></span>
                </button>

                <div x-show="showPasswordFields" x-collapse class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div class="space-y-4">
                        <flux:input label="New Password" wire:model.defer="password" type="password"
                            placeholder="••••••••" icon="lock" :required="showPasswordFields" />
                    </div>
                    <div class="space-y-4">
                        <flux:input label="Confirm Password" wire:model.defer="password_confirmation" type="password"
                            placeholder="••••••••" icon="lock" :required="showPasswordFields" />
                        <flux:text class="text-xs text-gray-500 dark:text-gray-400">
                            Password must be at least 8 characters long
                        </flux:text>
                    </div>
                </div>
            </div> --}}

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                <flux:button variant="ghost" class="cursor-pointer" type="button" @click="$wire.closeForm()">
                    Cancel
                </flux:button>
                {{-- <flux:button variant="ghost" type="button"
                    x-on:click="$flux.modal('edit-user-'.$user->id).close()">
                    Cancel
                </flux:button> --}}
                <div class="flex gap-3">
                    <flux:button variant="outline" type="button" @click="$wire.resetForm()">
                        Reset
                    </flux:button>
                    <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                        <span wire:loading wire:target="updateUser">
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
                        <span wire:loading.remove wire:target="updateUser">
                            Save Changes
                        </span>
                    </flux:button>
                </div>
            </div>
        </form>
    </div>
</flux:modal>
