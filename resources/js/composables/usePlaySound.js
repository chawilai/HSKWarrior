// composables/usePlaySound.js
import { ref } from "vue";
import tts2 from "@/azure_tts.js";

export function usePlaySound() {
  const shouldStop = ref(false);
  const playingWord = ref("");
  const currentPlaybackId = ref(0);

  const playSound = (input) => {
    const tts = new tts2();
    currentPlaybackId.value++;
    const playbackId = currentPlaybackId.value;
    shouldStop.value = false;

    tts.rate(0.5);
    tts.volume(1);
    tts.pitch(1.3);

    function processSound(sound) {
      return new Promise((resolve) => {
        tts.speak2(sound);
        playingWord.value = sound;
        resolve();
      });
    }

    function sleep(ms) {
      return new Promise((resolve) => setTimeout(resolve, ms));
    }

    async function processArray(sounds) {
      for (const sound of sounds) {
        if (shouldStop.value || playbackId !== currentPlaybackId.value) {
          break;
        }
        await processSound(sound);
        await sleep(1000);
      }
    }

    if (Array.isArray(input)) {
      processArray(input);
    } else {
      processSound(input);
    }
  };

  const stopPlayback = () => {
    shouldStop.value = true;
  };

  return {
    playSound,
    stopPlayback,
    playingWord,
    shouldStop,
  };
}
