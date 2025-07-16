<!-- In your layout file -->
<div x-data="toast" x-show="show" x-transition class="fixed bottom-4 right-4 z-50">
    <div class="px-6 py-4 rounded shadow-lg" :class="{
    'bg-green-500 text-white': type === 'success',
    'bg-red-500 text-white': type === 'error',
    'bg-yellow-500 text-white': type === 'warning',
    'bg-blue-500 text-white': type === 'info'
  }">
        <div x-text="message"></div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('toast', () => ({
            show: false,
            message: '',
            type: 'success',
            timeout: null,

            init() {
                window.addEventListener('toast', (e) => {
                    this.message = e.detail.message
                    this.type = e.detail.type || 'success'
                    this.showToast()
                })

                Livewire.on('toast', (data) => this.showToast(data))
            },

            showToast(data = null) {
                if (data) {
                    this.message = data.message
                    this.type = data.type || 'success'
                }

                this.show = true
                clearTimeout(this.timeout)
                this.timeout = setTimeout(() => this.show = false, 3000)
            }
        }))
    })
</script>