@props(['expiresAt'])

<div 
    x-data="cartExpirationTimer('{{ $expiresAt }}')"
    class="text-sm text-gray-500"
>
    Expires in <span x-text="formattedTime" class="font-medium text-amber-600"></span>
</div>

@script
<script>
    Alpine.data('cartExpirationTimer', (expiresAtString) => ({
        expiresAt: new Date(expiresAtString).getTime(),
        now: new Date().getTime(),
        timeRemaining: 0,
        formattedTime: '',
        
        init() {
            this.updateTimer();
            setInterval(() => this.updateTimer(), 1000);
        },

        updateTimer() {
            this.now = new Date().getTime();
            const distance = this.expiresAt - this.now;

            if (distance < 0) {
                this.timeRemaining = 0;
                this.formattedTime = 'Expired';
                return;
            }

            this.timeRemaining = distance;
            
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            this.formattedTime = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }
    }));
</script>
@endscript
