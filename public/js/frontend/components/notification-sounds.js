/**
 * Notification Sounds Generator
 * Creates simple notification sounds using Web Audio API
 */
class NotificationSounds {
    constructor() {
        this.audioContext = null;
        this.sounds = {};
        this.init();
    }

    init() {
        try {
            this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
            this.generateSounds();
        } catch (error) {
            console.warn('NotificationSounds: Audio context not available', error);
        }
    }

    /**
     * Generate different notification sounds
     */
    generateSounds() {
        // Default notification sound - gentle beep
        this.sounds.default = this.createBeepSound(800, 0.1, 0.3);
        
        // System notification - lower tone
        this.sounds.system = this.createBeepSound(600, 0.15, 0.4);
        
        // Message notification - double beep
        this.sounds.message = this.createDoubleBeepSound(900, 0.1, 0.25);
        
        // Follow notification - ascending tone
        this.sounds.follow = this.createAscendingSound(600, 900, 0.2, 0.3);
        
        // Like notification - short high beep
        this.sounds.like = this.createBeepSound(1200, 0.08, 0.2);
        
        // Showcase notification - chord
        this.sounds.showcase = this.createChordSound([800, 1000, 1200], 0.15, 0.3);
        
        // Order notification - cash register style
        this.sounds.order = this.createCashRegisterSound();
        
        // Warning notification - urgent beep
        this.sounds.warning = this.createBeepSound(400, 0.2, 0.5);
        
        // Success notification - pleasant chime
        this.sounds.success = this.createSuccessChime();
        
        // Thread notification - bubble pop
        this.sounds.thread = this.createBubbleSound();
        
        // Reply notification - soft ping
        this.sounds.reply = this.createBeepSound(1000, 0.12, 0.25);
        
        // Mention notification - attention sound
        this.sounds.mention = this.createTripleBeepSound(800, 0.08, 0.2);
    }

    /**
     * Create a simple beep sound
     */
    createBeepSound(frequency, duration, volume = 0.3) {
        return () => {
            if (!this.audioContext) return;

            const oscillator = this.audioContext.createOscillator();
            const gainNode = this.audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(this.audioContext.destination);

            oscillator.frequency.setValueAtTime(frequency, this.audioContext.currentTime);
            oscillator.type = 'sine';

            gainNode.gain.setValueAtTime(0, this.audioContext.currentTime);
            gainNode.gain.linearRampToValueAtTime(volume, this.audioContext.currentTime + 0.01);
            gainNode.gain.exponentialRampToValueAtTime(0.001, this.audioContext.currentTime + duration);

            oscillator.start(this.audioContext.currentTime);
            oscillator.stop(this.audioContext.currentTime + duration);
        };
    }

    /**
     * Create a double beep sound
     */
    createDoubleBeepSound(frequency, duration, volume = 0.3) {
        return () => {
            if (!this.audioContext) return;

            // First beep
            setTimeout(() => {
                this.createBeepSound(frequency, duration, volume)();
            }, 0);

            // Second beep
            setTimeout(() => {
                this.createBeepSound(frequency, duration, volume)();
            }, duration * 1000 + 50);
        };
    }

    /**
     * Create a triple beep sound
     */
    createTripleBeepSound(frequency, duration, volume = 0.3) {
        return () => {
            if (!this.audioContext) return;

            // Three quick beeps
            for (let i = 0; i < 3; i++) {
                setTimeout(() => {
                    this.createBeepSound(frequency, duration, volume)();
                }, i * (duration * 1000 + 30));
            }
        };
    }

    /**
     * Create an ascending tone
     */
    createAscendingSound(startFreq, endFreq, duration, volume = 0.3) {
        return () => {
            if (!this.audioContext) return;

            const oscillator = this.audioContext.createOscillator();
            const gainNode = this.audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(this.audioContext.destination);

            oscillator.frequency.setValueAtTime(startFreq, this.audioContext.currentTime);
            oscillator.frequency.linearRampToValueAtTime(endFreq, this.audioContext.currentTime + duration);
            oscillator.type = 'sine';

            gainNode.gain.setValueAtTime(0, this.audioContext.currentTime);
            gainNode.gain.linearRampToValueAtTime(volume, this.audioContext.currentTime + 0.01);
            gainNode.gain.exponentialRampToValueAtTime(0.001, this.audioContext.currentTime + duration);

            oscillator.start(this.audioContext.currentTime);
            oscillator.stop(this.audioContext.currentTime + duration);
        };
    }

    /**
     * Create a chord sound
     */
    createChordSound(frequencies, duration, volume = 0.2) {
        return () => {
            if (!this.audioContext) return;

            frequencies.forEach(freq => {
                this.createBeepSound(freq, duration, volume / frequencies.length)();
            });
        };
    }

    /**
     * Create a cash register style sound
     */
    createCashRegisterSound() {
        return () => {
            if (!this.audioContext) return;

            // Quick ascending notes
            const notes = [523, 659, 784]; // C, E, G
            notes.forEach((freq, index) => {
                setTimeout(() => {
                    this.createBeepSound(freq, 0.1, 0.2)();
                }, index * 50);
            });
        };
    }

    /**
     * Create a success chime
     */
    createSuccessChime() {
        return () => {
            if (!this.audioContext) return;

            // Pleasant ascending chime
            const notes = [523, 659, 784, 1047]; // C, E, G, C
            notes.forEach((freq, index) => {
                setTimeout(() => {
                    this.createBeepSound(freq, 0.15, 0.15)();
                }, index * 80);
            });
        };
    }

    /**
     * Create a bubble pop sound
     */
    createBubbleSound() {
        return () => {
            if (!this.audioContext) return;

            const oscillator = this.audioContext.createOscillator();
            const gainNode = this.audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(this.audioContext.destination);

            oscillator.frequency.setValueAtTime(800, this.audioContext.currentTime);
            oscillator.frequency.exponentialRampToValueAtTime(200, this.audioContext.currentTime + 0.1);
            oscillator.type = 'sine';

            gainNode.gain.setValueAtTime(0, this.audioContext.currentTime);
            gainNode.gain.linearRampToValueAtTime(0.3, this.audioContext.currentTime + 0.01);
            gainNode.gain.exponentialRampToValueAtTime(0.001, this.audioContext.currentTime + 0.1);

            oscillator.start(this.audioContext.currentTime);
            oscillator.stop(this.audioContext.currentTime + 0.1);
        };
    }

    /**
     * Play sound by type
     */
    playSound(type) {
        const soundFunction = this.sounds[type] || this.sounds.default;
        if (soundFunction) {
            try {
                soundFunction();
            } catch (error) {
                console.warn('NotificationSounds: Failed to play sound', error);
            }
        }
    }

    /**
     * Test all sounds
     */
    testAllSounds() {
        const soundTypes = Object.keys(this.sounds);
        soundTypes.forEach((type, index) => {
            setTimeout(() => {
                console.log(`Playing sound: ${type}`);
                this.playSound(type);
            }, index * 1000);
        });
    }
}

// Export for use in other modules
window.NotificationSounds = NotificationSounds;
