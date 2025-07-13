<div>
    <div class="flex items-center justify-between p-4">
        <flux:heading size="lg">Employee management</flux:heading>


        <div class="flex items-center gap-4">
            <!-- Search Input -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <flux:input wire:model.live="search" placeholder="Search employees..." class="pl-10 w-64" />
            </div>

            <!-- Trigger Button -->
            <flux:modal.trigger name="add-employee">
                <flux:button variant="primary" class="inline-flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Employee
                </flux:button>
            </flux:modal.trigger>

            <!-- Modal -->
            <flux:modal name="add-employee" class="md:w-[800px]">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">Add New Employee</flux:heading>
                        <flux:text class="mt-2 text-gray-600 dark:text-gray-400">Assign employment details to an
                            existing user</flux:text>
                        <x-action-message class="me-3" on="employee-added" />
                    </div>

                    <form wire:submit.prevent="saveEmployee">
                        <!-- User Search/Select Field -->
                        <div class="mb-6" x-data="{ open: false }">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select
                                User</label>
                            <div class="relative">
                                <div class="flex items-center">
                                    <flux:input type="text" wire:model.live="userSearch" x-on:focus="open = true"
                                        x-on:click.away="open = false" placeholder="Search users by name or email..."
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                                    <button type="button" class="ml-2 p-2 text-gray-400 hover:text-gray-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                </div>
                                <!-- Dropdown Results -->
                                <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 shadow-lg rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto max-h-60 focus:outline-none sm:text-sm">
                                    <template x-if="!$wire.users.length">
                                        <div class="px-4 py-2 text-gray-500 dark:text-gray-400">
                                            No users found
                                        </div>
                                    </template>
                                    <template x-for="user in $wire.users" :key="user.id">
                                        <button type="button" x-on:click="$wire.selectUser(user); open = false"
                                            class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center">
                                            <img :src="user.avatar_url || 'https://ui-avatars.com/api/?name='+encodeURIComponent(user.name)+'&background=random'"
                                                class="h-8 w-8 rounded-full mr-3" :alt="user.name">
                                            <div>
                                                <div x-text="user.name"
                                                    class="font-medium text-gray-900 dark:text-white"></div>
                                                <div x-text="user.email"
                                                    class="text-sm text-gray-500 dark:text-gray-400"></div>
                                            </div>
                                        </button>
                                    </template>
                                </div>
                            </div><!-- Selected User Display -->
                            <template x-if="$wire.selectedUser">
                                <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-md flex items-center">
                                    <img :src="$wire.selectedUser.avatar_url || 'https://ui-avatars.com/api/?name='+encodeURIComponent($wire.selectedUser.name)+'&background=random'"
                                        class="h-10 w-10 rounded-full mr-3" :alt="$wire.selectedUser.name">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white"
                                            x-text="$wire.selectedUser.name"></div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400"
                                            x-text="$wire.selectedUser.email"></div>
                                    </div>
                                    <button type="button" x-on:click="$wire.resetUserSelection()"
                                        class="ml-auto text-red-500 hover:text-red-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <!-- Staff ID Field -->
                        <!-- Staff ID Field -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Staff
                                ID</label>
                            <div class="relative">
                                <flux:input type="text" wire:model="staff_id"
                                    class="block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 dark:bg-gray-700 cursor-not-allowed"
                                    readonly disabled />
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                            </div>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Automatically generated 4-digit ID
                            </p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Left Column - Basic Information -->
                            <div class="space-y-4">
                                <!-- Non-editable Staff ID Field -->


                                <flux:select label="Department" wire:model="department_id" class="w-full"
                                    icon="office-building" required>
                                    <option value="">Select Department</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </flux:select>

                                <flux:select label="Position" wire:model="position_id" class="w-full" icon="briefcase">
                                    <option value="">Select Position</option>
                                    @foreach ($positions as $position)
                                        <option value="{{ $position->id }}">{{ $position->title }}</option>
                                    @endforeach
                                </flux:select>



                            </div>

                            <!-- Right Column - Employment Details -->
                            <div class="space-y-4">


                                <div class="grid grid-cols-2 gap-4">
                                    <flux:input type="number" label="Level" wire:model="level" placeholder="e.g. L4"
                                        class="w-full" />
                                    <flux:input type="number" label="Step" wire:model="step" placeholder="e.g. S2"
                                        class="w-full" />
                                </div>

                                <flux:input label="Hire Date" wire:model="hire_date" type="date" class="w-full"
                                    icon="calendar" />
                            </div>
                        </div>

                        <!-- Bank Details Section -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <flux:heading size="md" class="mb-4">Bank Information</flux:heading>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <flux:select label="Bank" wire:model="bank_id" class="w-full" icon="briefcase">
                                    <option value="">Select Bank</option>
                                    @foreach ($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                    @endforeach
                                </flux:select>

                                <flux:input label="Account Number" wire:model="account_number" placeholder="1234567890"
                                    class="w-full" icon="credit-card" />
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-between pt-6 mt-6 border-t border-gray-200">
                            <flux:button variant="ghost" type="button" x-on:click="$flux.modal('add-employee').close()">
                                Cancel
                            </flux:button>
                            <div class="flex gap-3">
                                <flux:button variant="outline" type="button" wire:click="resetForm">
                                    Reset Form
                                </flux:button>
                                <flux:button variant="primary" type="submit">
                                    Create Employee
                                </flux:button>
                            </div>
                        </div>
                    </form>
                </div>
            </flux:modal>
        </div>





        <!-- Livewire component example code...
    use \Livewire\WithPagination;

    public $sortBy = 'date';
    public $sortDirection = 'desc';

    public function sort($column) {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    #[\Livewire\Attributes\Computed]
    public function orders()
    {
        return \App\Models\Order::query()
            ->tap(fn ($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate(5);
    }
-->
    </div>
    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
        <!-- Success Message -->
        <x-action-message
            class="bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-200 px-4 py-3 rounded-md"
            on="employee-added">
            {{ __('Employee added successfully!') }}
        </x-action-message>
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <!-- Staff ID -->
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            <div class="flex items-center gap-1">
                                Staff ID
                                <button type="button" wire:click="sort('staff_id')"
                                    class="text-gray-400 hover:text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                    </svg>
                                </button>
                            </div>
                        </th>

                        <!-- Employee -->
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            <div class="flex items-center gap-1">
                                Employee
                                <button type="button" wire:click="sort('user.name')"
                                    class="text-gray-400 hover:text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                    </svg>
                                </button>
                            </div>
                        </th>

                        <!-- Department -->
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            <div class="flex items-center gap-1">
                                Department
                                <button type="button" wire:click="sort('department.name')"
                                    class="text-gray-400 hover:text-gray-500">
                                    @if($sortBy === 'department.name')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 15l7-7 7 7" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    @endif
                                </button>
                            </div>
                        </th>

                        <!-- Position -->
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            <div class="flex items-center gap-1">
                                Position
                                <button type="button" wire:click="sort('position.title')"
                                    class="text-gray-400 hover:text-gray-500">
                                    @if($sortBy === 'position.title')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 15l7-7 7 7" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    @endif
                                </button>
                            </div>
                        </th>

                        <!-- Level/Step -->
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            <div class="flex items-center gap-1">
                                Level/Step
                                <button type="button" wire:click="sort('level')"
                                    class="text-gray-400 hover:text-gray-500">
                                    @if($sortBy === 'level')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 15l7-7 7 7" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    @endif
                                </button>
                            </div>
                        </th>

                        <!-- Hire Date -->
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            <div class="flex items-center gap-1">
                                Hire Date
                                <button type="button" wire:click="sort('hire_date')"
                                    class="text-gray-400 hover:text-gray-500">
                                    @if($sortBy === 'hire_date')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 15l7-7 7 7" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    @endif
                                </button>
                            </div>
                        </th>

                        <!-- bank name -->
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            <div class="flex items-center gap-1">
                                Bank Name

                                <button type="button" wire:click="sort('status')"
                                    class="text-gray-400 hover:text-gray-500">
                                    @if($sortBy === 'status')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 15l7-7 7 7" />
                                    </svg>
                                    @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                    @endif
                                </button>
                            </div>
                        </th>
                        <!-- bank name -->
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            <div class="flex items-center gap-1">
                                Account

                                <button type="button" wire:click="sort('status')" class="text-gray-400 hover:text-gray-500">
                                    @if($sortBy === 'status')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    @endif
                                </button>
                            </div>
                        </th>

                        <!-- Actions -->
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($this->employees as $employee)
                        <tr wire:key="employee-{{ $employee->id }}"
                            class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <!-- Staff ID -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $employee->staff_id }}
                            </td>

                            <!-- Employee Info -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full"
                                            src="{{ $employee->user?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($employee->user?->name ?? '') . '&background=random' }}"
                                            alt="{{ $employee->user?->name ?? '' }}">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $employee->user?->name ?? 'N/A' }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $employee->user?->email ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Department -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $employee->department?->name ?? 'N/A' }}
                            </td>

                            <!-- Position -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $employee->position?->title ?? 'N/A' }}
                            </td>

                            <!-- Level/Step -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $employee->level ? 'L' . $employee->level : 'N/A' }} /
                                {{ $employee->step ? 'S' . $employee->step : 'N/A' }}
                            </td>

                            <!-- Hire Date -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('M d, Y') : 'N/A' }}
                            </td>

                            <!-- Bank Name -->
                            <td class="px-6 py-4 whitespace-nowrap">


                                   {{ $employee->bank->name }}

                            </td>
                            <!-- Account Number -->
                            <td class="px-6 py-4 whitespace-nowrap">


                                {{ $employee->account_number }}

                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button wire:click="editEmployee({{ $employee->id }})"
                                        class="text-blue-600 hover:text-blue-900">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $employee->id }})"
                                        class="text-red-600 hover:text-red-900">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center py-8">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No employees found
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by adding a new
                                        employee.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($this->employees->hasPages())
            <div
                class="bg-white dark:bg-gray-900 px-4 py-3 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 sm:px-6 rounded-b-lg">
                <div class="flex-1 flex justify-between sm:hidden">
                    @if($this->employees->onFirstPage())
                        <span
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-white dark:bg-gray-800 cursor-not-allowed">
                            Previous
                        </span>
                    @else
                        <button wire:click="previousPage"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Previous
                        </button>
                    @endif

                    @if($this->employees->hasMorePages())
                        <button wire:click="nextPage"
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Next
                        </button>
                    @else
                        <span
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-white dark:bg-gray-800 cursor-not-allowed">
                            Next
                        </span>
                    @endif
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            Showing
                            <span class="font-medium">{{ $this->employees->firstItem() }}</span>
                            to
                            <span class="font-medium">{{ $this->employees->lastItem() }}</span>
                            of
                            <span class="font-medium">{{ $this->employees->total() }}</span>
                            results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            @if($this->employees->onFirstPage())
                                <span
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white dark:bg-gray-800 text-sm font-medium text-gray-300 cursor-not-allowed">
                                    <span class="sr-only">Previous</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                </span>
                            @else
                                <button wire:click="previousPage"
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <span class="sr-only">Previous</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                            @endif

                            @foreach($employees->getUrlRange(1, $employees->lastPage()) as $page => $url)
                                @if($page == $employees->currentPage())
                                    <span aria-current="page"
                                        class="relative inline-flex items-center px-4 py-2 border border-blue-500 text-sm font-medium text-blue-600 dark:text-blue-300 bg-blue-50 dark:bg-blue-900">
                                        {{ $page }}
                                    </span>
                                @else
                                    <button wire:click="gotoPage({{ $page }})"
                                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        {{ $page }}
                                    </button>
                                @endif
                            @endforeach

                            @if($employees->hasMorePages())
                                <button wire:click="nextPage"
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <span class="sr-only">Next</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            @else
                                <span
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white dark:bg-gray-800 text-sm font-medium text-gray-300 cursor-not-allowed">
                                    <span class="sr-only">Next</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </span>
                            @endif
                        </nav>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
