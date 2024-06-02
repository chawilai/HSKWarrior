<script setup>
import { onMounted, ref } from "vue";
import SignaturePad from "signature_pad";

const props = defineProps({
  width: { type: Number, default: 200 },
  height: { type: Number, default: 200 },
  backgroundColor: { type: String, default: "rgba(255, 255, 255, 0)" },
  penColor: { type: String, default: "#777" },
  minWidth: { type: Number, default: 1 },
  maxWidth: { type: Number, default: 6 },
  dotSize: { type: [Number, Boolean], default: false },
  throttle: { type: [Number], default: 16 },
  minDistance: { type: [Number], default: 5 },
  saveButtonText: { type: String, default: "Save" },
  clearButtonText: { type: String, default: "Clear" },
  canvasId: { type: String, default: "signature-pad" },
  canvasClass: { type: String, default: "bg-red-100" },
  wrapperClass: { type: String, default: "" },
  haveBtn: { type: Boolean, default: false },
  haveSave: { type: Boolean, default: false },
  haveClear: { type: Boolean, default: false },
});

const emit = defineEmits(["updateData"]);
const canvaData = ref([]);

const handleChange = (event) => {
  const updatedData = {
    id: "xxxx",
    data: "yyyy",
  };
  emit("updateData", updatedData);
};

// Watch for changes in the canvas if needed
// watch(() => /* your reactive data */, (newValue) => {
//   emit('updateData', newValue);
// });

const canvasRef = ref(null);
const signaturePad = ref(null);


onMounted(() => {
  signaturePad.value = new SignaturePad(document.getElementById(props.canvasId), {
    backgroundColor: props.backgroundColor,
    penColor: props.penColor,
    minWidth: props.minWidth,
    maxWidth: props.maxWidth,
    dotSize: props.dotSize,
    throttle: props.throttle,
    minDistance: props.minDistance,
  });
  if (props.haveBtn) {
    var saveButton = document.getElementById(`save_${props.canvasId}`);
    var cancelButton = document.getElementById(`clear_${props.canvasId}`);
    var undoButton = document.getElementById(`undo_${props.canvasId}`);

    saveButton.addEventListener("click", function () {
      canvaData.value = signaturePad.value.toDataURL("image/png");
      console.log(canvaData.value);
      // Send data to server instead...
      // window.open(data);
    });

    cancelButton.addEventListener("click", function () {
      signaturePad.value.clear();
    });

    undoButton.addEventListener("click", function () {
      canvaData.value = signaturePad.value.toData();
      console.log(canvaData.value);
      if (canvaData.value) {
        canvaData.value.pop(); // remove the last dot or line
        signaturePad.fromData(canvaData.value);
      }
    });
  }
});
</script>

<template>
  <div
    class="w-15 h-15 border-t border-b border-r border-red first:border-l last:border-r"
    :class="wrapperClass"
  >
    <div class="wrapper">
      <canvas
        :id="canvasId"
        class="absolute left-0 top-0"
        :class="canvasClass"
        :width="width"
        :height="height"
        :style="{ width: width + 'px', height: height + 'px' }"
      ></canvas>
    </div>
  </div>
</template>

<style scoped>
.wrapper {
  position: relative;
  -moz-user-select: none;
  -webkit-user-select: none;
  -ms-user-select: none;
  user-select: none;
}
</style>
