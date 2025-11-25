class AzureTTS {
    constructor() {
        // Default settings - ปรับได้ที่นี่เลย
        this.defaults = {
            rate: 0.7,      // ความเร็ว (0.1-10, แนะนำ 0.5-1.5)
            volume: 80,     // เสียงดัง (0-100)
            pitch: 1.0,     // ระดับเสียง (0.1-2.0)
            voice: 'zh-CN-XiaoxiaoNeural'
            // ตัวเลือกเสียงภาษาจีน (Mandarin):
            // หญิง:
            // 'zh-CN-XiaoxiaoNeural' (Default - เสียงใส ฟังสบาย)
            // 'zh-CN-XiaoyiNeural' (เสียงดิจิตอล สดใส)
            // 'zh-CN-LiaoningNeural' (สำเนียงเหลียวหนิง)
            // 'zh-CN-SichuanNeural' (สำเนียงเสฉวน)
            //
            // ชาย:
            // 'zh-CN-YunxiNeural' (เสียงหนุ่ม สุภาพ)
            // 'zh-CN-YunjianNeural' (เสียงผู้ใหญ่ ทางการ)
            // 'zh-CN-YunyangNeural' (เสียงผู้ประกาศข่าว)
        };
        
        this.currentAudio = null;
    }

    /**
     * พูดข้อความ
     * @param {string} textToSpeak - ข้อความที่จะพูด
     * @param {string} langCode - รหัสภาษา (ไม่ได้ใช้แล้ว แต่เก็บไว้ compatibility)
     * @param {object} options - ตัวเลือกเพิ่มเติม { rate, volume, pitch, voice }
     */
    async speak2(textToSpeak, langCode = 'zh-CN', options = {}) {
        // Cancel any previous playback
        this.cancel2();

        // Merge options with defaults
        const settings = {
            rate: options.rate ?? this.defaults.rate,
            volume: options.volume ?? this.defaults.volume,
            pitch: options.pitch ?? this.defaults.pitch,
            voice: options.voice ?? this.defaults.voice,
        };

        try {
            const response = await fetch('/api/azure-tts', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({
                    text: textToSpeak,
                    rate: settings.rate,
                    volume: settings.volume,
                    pitch: settings.pitch,
                    voice: settings.voice,
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

    // เก็บไว้เพื่อ backward compatibility (แต่ไม่แนะนำให้ใช้แล้ว)
    volume(volume_val) {
        console.warn('tts.volume() is deprecated. Use speak2(text, lang, { volume: value }) instead.');
        this.defaults.volume = Math.round(Number(volume_val) * 100);
    }

    rate(rate_val) {
        console.warn('tts.rate() is deprecated. Use speak2(text, lang, { rate: value }) instead.');
        this.defaults.rate = Number(rate_val);
    }

    pitch(pitch_val) {
        console.warn('tts.pitch() is deprecated. Use speak2(text, lang, { pitch: value }) instead.');
        this.defaults.pitch = Number(pitch_val);
    }
}

export default AzureTTS;
