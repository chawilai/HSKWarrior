class AzureTTS {
    constructor() {
        this.u_rate = 1.0;
        this.u_volume = 100;
        this.u_pitch = 1.0;
        this.currentAudio = null;
    }

    async speak2(textToSpeak, langCode = 'zh-CN') {
        // Cancel any previous playback
        this.cancel2();

        try {
            const response = await fetch('/api/azure-tts', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({
                    text: textToSpeak,
                    rate: this.u_rate,
                    volume: this.u_volume,
                    pitch: this.u_pitch,
                    voice: 'zh-CN-XiaoxiaoNeural',
                }),
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const audioBlob = await response.blob();
            const audioUrl = URL.createObjectURL(audioBlob);
            
            this.currentAudio = new Audio(audioUrl);
            
            // Clean up blob URL after playback
            this.currentAudio.addEventListener('ended', () => {
                URL.revokeObjectURL(audioUrl);
            });

            await this.currentAudio.play();
            
        } catch (error) {
            console.error('Azure TTS Error:', error);
        }
    }

    cancel2() {
        if (this.currentAudio) {
            this.currentAudio.pause();
            this.currentAudio.currentTime = 0;
            this.currentAudio = null;
        }
    }

    volume(volume_val) {
        // Convert 0-1 to 0-100 for Azure API
        this.u_volume = Math.round(Number(volume_val) * 100);
    }

    rate(rate_val) {
        // Clamp to Azure supported range
        let r = Number(rate_val);
        if (r < 0.5) r = 0.5;
        if (r > 2.0) r = 2.0;
        this.u_rate = r;
    }

    pitch(pitch_val) {
        this.u_pitch = Number(pitch_val);
    }
}

export default AzureTTS;
