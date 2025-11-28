// composables/usePlaySound.js
import { ref } from "vue";
import tts2 from "@/azure_tts.js";

export function usePlaySound() {
  const shouldStop = ref(false);
  const playingWord = ref("");
  const currentPlaybackId = ref(0);

  const playSound = (input, onAllSoundsFinished) => {
    const tts = new tts2();
    currentPlaybackId.value++;
    const playbackId = currentPlaybackId.value;
    shouldStop.value = false;

    async function processSound(sound) {
      playingWord.value = sound;
      // The speak2 method now returns a promise that resolves when the sound ends
      await tts.speak2(sound);
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
        await sleep(1000); // Optional delay between sounds
      }
      // After the loop, if not stopped, call the final callback
      if (!shouldStop.value && onAllSoundsFinished) {
        onAllSoundsFinished();
      }
    }

    if (Array.isArray(input)) {
      processArray(input);
    } else {
      processSound(input).then(() => {
        // After a single sound, if not stopped, call the final callback
        if (!shouldStop.value && onAllSoundsFinished) {
          onAllSoundsFinished();
        }
      });
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
