<div>
    <div class="flex items-center justify-between p-4">
        <flux:heading size="lg">Employee allowance</flux:heading>

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



        <div class="flex items-center gap-4">
            <!-- Search Input -->
            {{-- <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <flux:input wire:model.live="search" placeholder="Search allowance..." class="pl-10 w-64" />
            </div> --}}




            <!-- Trigger Button -->
            <flux:modal.trigger name="add-allowance">
                <flux:button variant="primary" class="inline-flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Allowance
                </flux:button>
            </flux:modal.trigger>

            <!-- Modal -->
            <flux:modal name="add-allowance" class="md:w-[800px]">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">Add New Allowance</flux:heading>
                        <flux:text class="mt-2 text-gray-600 dark:text-gray-400">Assign allowance to an employee
                        </flux:text>
                        <x-action-message class="me-3" on="allowance-added" />
                    </div>

                    <form wire:submit.prevent="saveAllowance">
                        <!-- Employee Search by Staff ID -->
                        <div class="mb-6" x-data="{ open: false }">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search
                                Employee by Staff ID</label>
                            <div class="relative">
                                <div class="flex items-center">
                                    <flux:input type="text" wire:model.live="staffIdSearch" x-on:focus="open = true"
                                        x-on:click.away="open = false" placeholder="Search by staff ID..."
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
                                    <template x-if="!$wire.employees.length">
                                        <div class="px-4 py-2 text-gray-500 dark:text-gray-400">
                                            No employees found
                                        </div>
                                    </template>
                                    <template x-for="employee in $wire.employees" :key="employee.id">
                                        <button type="button" x-on:click="$wire.selectEmployee(employee); open = false"
                                            class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center">
                                            <div class="mr-3">
                                                <div class="font-medium text-gray-900 dark:text-white"
                                                    x-text="employee.staff_id"></div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400"
                                                    x-text="employee.user.name"></div>
                                            </div>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <!-- Selected Employee Display -->
                            <template x-if="$wire.selectedEmployee">
                                <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-md flex items-center">
                                    <div class="mr-3">
                                        <div class="font-medium text-gray-900 dark:text-white"
                                            x-text="$wire.selectedEmployee.staff_id"></div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400"
                                            x-text="$wire.selectedEmployee.user.name"></div>
                                    </div>
                                    <button type="button" x-on:click="$wire.resetEmployeeSelection()"
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

                        <!-- Allowance Type -->
                        <div class="mb-6">
                            <flux:select label="Allowance Type" wire:model="allowance_type_id" class="w-full"
                                icon="document-text" required>
                                <option value="">Select Allowance Type</option>
                                @foreach ($allowanceTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </flux:select>
                            @error('allowance_type_id') <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Amount -->
                        <div class="mb-6">
                            <flux:input type="number" label="Amount" wire:model.defer="amount" placeholder="0.00"
                                class="w-full" icon="currency-dollar" step="0.01" min="0" required />
                            @error('amount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Note -->
                        <div class="mb-6">
                            <flux:textarea label="Note (Optional)" wire:model.defer="note"
                                placeholder="Enter any notes..." class="w-full" rows="3" />
                            @error('note') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-between pt-6 mt-6 border-t border-gray-200">
                            <flux:button variant="ghost" type="button"
                                x-on:click="$flux.modal('add-allowance').close()">
                                Cancel
                            </flux:button>
                            <div class="flex gap-3">
                                <flux:button variant="outline" type="button" wire:click="resetForm">
                                    Reset Form
                                </flux:button>
                                <flux:button variant="primary" type="submit">
                                    Add Allowance
                                </flux:button>
                            </div>
                        </div>
                    </form>
                </div>
            </flux:modal>
        </div>





    </div>
    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
        <!-- Success Message -->
        <!-- Success Message -->
        <x-action-message
            class="bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-200 px-4 py-3 rounded-md"
            on="allowance-added">
            {{ __('Allowance added successfully!') }}
        </x-action-message>
        <!-- Success Message -->
        <x-action-message
            class="bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-200 px-4 py-3 rounded-md"
            on="allowance-updated">
            {{ __('Allowance updated successfully!') }}
        </x-action-message>
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>

                        <!-- Employee -->
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            <div class="flex items-center gap-1">
                                Employee
                                <button type="button" wire:click="sort('employee.user.name')"
                                    class="text-gray-400 hover:text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                    </svg>
                                </button>
                            </div>
                        </th>

                        <!-- Staff ID -->
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            <div class="flex items-center gap-1">
                                Staff ID
                                <button type="button" wire:click="sort('employee.staff_id')"
                                    class="text-gray-400 hover:text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                    </svg>
                                </button>
                            </div>
                        </th>

                        <!-- allowance Type -->
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            <div class="flex items-center gap-1">
                                allowance Type
                                <button type="button" wire:click="sort('allowance_type.name')"
                                    class="text-gray-400 hover:text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                    </svg>
                                </button>
                            </div>
                        </th>

                        <!-- Amount -->
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            <div class="flex items-center gap-1">
                                Amount
                                {{-- <button type="button" wire:click="sort('amount')"
                                    class="text-gray-400 hover:text-gray-500">
                                    @if($sortBy === 'amount')
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
                                </button> --}}
                            </div>
                        </th>

                        <!-- Date -->
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            <div class="flex items-center gap-1">
                                Date
                                {{-- <button type="button" wire:click="sort('created_at')"
                                    class="text-gray-400 hover:text-gray-500">
                                    @if($sortBy === 'created_at')
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
                                </button> --}}
                            </div>
                        </th>

                        <!-- Note -->
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Note
                        </th>

                        <!-- Actions -->
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($this->allowances as $allowance)
                        {{-- @include('livewire.admin.employees.modals.edit-allowance')
                        @include('livewire.admin.employees.modals.delete-allowance') --}}
                        <tr wire:key="allowance-{{ $allowance->id }}"
                            class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <!-- Employee Info -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full"
                                            src="{{ $allowance->employee->user?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($allowance->employee->user?->name ?? '') . '&background=random' }}"
                                            alt="{{ $allowance->employee->user?->name ?? '' }}">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $allowance->employee->user?->name ?? 'N/A' }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $allowance->employee->user?->email ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Staff ID -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $allowance->employee->staff_id }}
                            </td>

                            <!-- Allowance Type -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $allowance->type->name }}
                            </td>

                            <!-- Amount -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ config('app.currency') }}{{ number_format($allowance->amount, 2) }}
                            </td>

                            <!-- Date -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $allowance->created_at->format('M d, Y') }}
                            </td>

                            <!-- Note -->
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">
                                {{ $allowance->note ?? 'N/A' }}
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <flux:modal.trigger :name="'edit-allowance-'.$allowance->id">
                                        <button wire:click="editAllowance({{ $allowance->id }})"
                                            class="text-blue-600 hover:text-blue-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                    </flux:modal.trigger>
                                    <flux:modal.trigger :name="'delete-allowance-'.$allowance->id">
                                        <button wire:click="confirmDelete({{ $allowance->id }})"
                                            class="text-red-600 hover:text-red-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </flux:modal.trigger>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center py-8">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No allowances found
                                    </h3>
                                    <wan class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by adding a new
                                        allowance.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($this->allowances->hasPages())
            <div
                class="bg-white dark:bg-gray-900 px-4 py-3 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 sm:px-6 rounded-b-lg">
                <div class="flex-1 flex justify-between sm:hidden">
                    @if($allowances->onFirstPage())
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

                    @if($this->allowances->hasMorePages())
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
                            <span class="font-medium">{{ $this->allowances->firstItem() }}</span>
                            to
                            <span class="font-medium">{{ $this->allowances->lastItem() }}</span>
                            of
                            <span class="font-medium">{{ $this->allowances->total() }}</span>
                            results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            @if($this->allowances->onFirstPage())
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

                            @foreach($this->allowances->getUrlRange(1, $this->allowances->lastPage()) as $page => $url)
                                @if($page == $this->allowances->currentPage())
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

                            @if($this->allowances->hasMorePages())
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