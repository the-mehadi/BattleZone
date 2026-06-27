

import Alpine from 'alpinejs';

window.countdownTimer = (targetTime) => ({
    targetTime,
    display: '00 : 00 : 00 : 00',
    started: false,
    intervalId: null,
    init() {
        this.updateCountdown();
        this.intervalId = setInterval(() => this.updateCountdown(), 1000);
    },
    updateCountdown() {
        const distance = new Date(this.targetTime).getTime() - Date.now();

        if (distance <= 0) {
            this.started = true;
            this.display = 'Match Started!';

            if (this.intervalId) {
                clearInterval(this.intervalId);
            }

            return;
        }

        const totalSeconds = Math.floor(distance / 1000);
        const days = Math.floor(totalSeconds / 86400);
        const hours = Math.floor((totalSeconds % 86400) / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;

        this.display = [days, hours, minutes, seconds]
            .map((value) => String(value).padStart(2, '0'))
            .join(' : ');
    },
});

window.Alpine = Alpine;

Alpine.start();
