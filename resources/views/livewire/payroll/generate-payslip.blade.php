<div>
    <!-- Confirmation Modal -->
    <div x-data="{ open: false }" x-show="open"
        @open-modal.window="if ($event.detail.name === 'confirm-generation') open = true"
        @close-modal.window="if ($event.detail.name === 'confirm-generation') open = false"
        class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="open" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div
                        class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/50">
                        <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white">Confirm Payslip Generation
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            This will generate payslips for all active employees
                        </p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <p class="font-medium text-gray-900 dark:text-white">
                            {{ $month }} {{ $year }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            {{ $employeeCount }} active employees will be processed
                        </p>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button @click="open = false" type="button"
                            class="inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                        <button wire:click="generate" @click="open = false" type="button"
                            class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Confirm Generation
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal - Similar structure to Confirmation Modal -->
    <flux:modal name="preview-payslips" max-width="md" wire:ignore.self>
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <flux:heading size="lg">Payslip Preview</flux:heading>
                <button @click="$dispatch('close-modal', { name: 'preview-payslips' })"
                    class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Period</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $previewData['month_year'] ?? '' }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Employees</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $previewData['employee_count'] ?? 0 }}
                        </p>
                    </div>
                </div>

                <div
                    class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-100 dark:border-blue-900/30">
                    <p class="text-sm text-blue-600 dark:text-blue-400">Estimated Total Payroll</p>
                    <p class="text-2xl font-bold text-blue-700 dark:text-blue-300">
                        {{ $currency ?? '₱' }}{{ $previewData['estimated_payroll'] ?? '0.00' }}
                    </p>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <flux:button variant="ghost" @click="$dispatch('close-modal', { name: 'preview-payslips' })">
                        Close
                    </flux:button>
                    <flux:button variant="primary"
                        @click="$dispatch('open-modal', { name: 'confirm-generation' }); $dispatch('close-modal', { name: 'preview-payslips' })">
                        Proceed to Generate
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>
    <!-- Dry Run Results Modal - Similar structure to Confirmation Modal -->
    <flux:modal name="dry-run-results" max-width="lg" wire:ignore.self>
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <flux:heading size="lg">Payroll Validation Results</flux:heading>
                <button @click="$dispatch('close-modal', { name: 'dry-run-results' })"
                    class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="space-y-4">
                @if(count($dryRunIssues) > 0)
                    @foreach($dryRunIssues as $issue)
                        <div
                            class="bg-{{ $issue['type'] == 'danger' ? 'red' : 'yellow' }}-50 dark:bg-{{ $issue['type'] == 'danger' ? 'red' : 'yellow' }}-900/20 p-4 rounded-lg border border-{{ $issue['type'] == 'danger' ? 'red' : 'yellow' }}-100 dark:border-{{ $issue['type'] == 'danger' ? 'red' : 'yellow' }}-900/30">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-{{ $issue['type'] == 'danger' ? 'red' : 'yellow' }}-500 dark:text-{{ $issue['type'] == 'danger' ? 'red' : 'yellow' }}-400"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div>
                                    <p
                                        class="font-medium text-{{ $issue['type'] == 'danger' ? 'red-700' : 'yellow-700' }} dark:text-{{ $issue['type'] == 'danger' ? 'red-400' : 'yellow-400' }}">
                                        {{ $issue['message'] }}
                                    </p>
                                    @if(!empty($issue['details']))
                                        <div
                                            class="mt-2 text-sm text-{{ $issue['type'] == 'danger' ? 'red-600' : 'yellow-600' }} dark:text-{{ $issue['type'] == 'danger' ? 'red-400' : 'yellow-400' }}">
                                            <ul class="list-disc pl-5 space-y-1">
                                                @foreach($issue['details'] as $detail)
                                                    <li>{{ $detail }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div
                        class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-100 dark:border-green-900/30">
                        <div class="flex items-center gap-3">
                            <svg class="h-5 w-5 text-green-500 dark:text-green-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <p class="font-medium text-green-700 dark:text-green-400">
                                No issues detected in payroll data.
                            </p>
                        </div>
                    </div>
                @endif

                <div class="flex justify-end gap-3 pt-4">
                    <flux:button variant="ghost" @click="$dispatch('close-modal', { name: 'dry-run-results' })">
                        Close
                    </flux:button>
                    @if(count($dryRunIssues) === 0)
                        <flux:button variant="primary"
                            @click="$dispatch('open-modal', { name: 'confirm-generation' }); $dispatch('close-modal', { name: 'dry-run-results' })">
                            Proceed to Generate
                        </flux:button>
                    @endif
                </div>
            </div>
        </div>
    </flux:modal>

    <!-- Main Form -->
    <div class="max-w-xl mx-auto bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                    Generate Monthly Payslips
                </h2>
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                    {{ now()->format('F Y') }}
                </span>
            </div>

            <div class="space-y-5">
                <!-- Month Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Month <span
                            class="text-red-500">*</span></label>
                    <select wire:model="month"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        @foreach($monthOptions as $option)
                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                    @error('month')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Year Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Year <span
                            class="text-red-500">*</span></label>
                    <input type="number" wire:model="year" min="2000" max="{{ $currentYear + 1 }}"
                        class="mt-1 block w-full shadow-sm sm:text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500">
                    @error('year')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Progress Indicator -->
                @if($showProgress)
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300">
                            <span>Processing payslips...</span>
                            <span>{{ $processed }} / {{ $total }}</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full"
                                style="width: {{ $total > 0 ? ($processed / $total) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                    <button wire:click="dryRun" wire:loading.attr="disabled"
                        class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <span wire:loading.remove wire:target="dryRun">
                            Validate Data
                        </span>
                        <span wire:loading wire:target="dryRun" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Validating...
                        </span>
                    </button>

                    <button wire:click="preview" wire:loading.attr="disabled"
                        class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <span wire:loading.remove wire:target="preview">
                            Preview Summary
                        </span>
                        <span wire:loading wire:target="preview" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Preparing...
                        </span>
                    </button>

                    <button @click="$dispatch('open-modal', { name: 'confirm-generation' })"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <span wire:loading.remove wire:target="generate">
                            Generate Payslips
                        </span>
                        <span wire:loading wire:target="generate" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Generating...
                        </span>
                    </button>
                </div>

                <!-- Help Text -->
                <div
                    class="text-sm text-gray-500 dark:text-gray-400 flex items-start gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p>Payslips will include base salary, allowances, and deductions for each employee.</p>
                        <p class="mt-1">Use "Validate Data" to check for potential issues before generation.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
