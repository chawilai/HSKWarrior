<template>
    <div class="min-h-screen bg-gray-100 p-4">
      <!-- Header -->
      <div class="text-center font-semibold text-lg mb-4">
        <h1>KhunThong</h1>
        <p class="text-sm text-gray-500">khunthong.kasikornbank.com</p>
      </div>

      <!-- Tab Navigation -->
      <div class="flex justify-center space-x-4 bg-white shadow-md rounded-lg p-2">
        <button
          class="flex-1 text-center py-2 rounded-md bg-gray-200 text-gray-600 font-medium"
        >
          ในกรุ๊ปไลน์
        </button>
        <button
          class="flex-1 text-center py-2 rounded-md bg-green-500 text-white font-medium"
        >
          แชทส่วนตัว <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs">ใหม่</span>
        </button>
      </div>

      <!-- Content Section -->
      <div class="mt-4 space-y-4">
        <!-- Create Bill Section -->
        <div class="bg-teal-100 rounded-lg p-4 flex items-center">
          <div class="flex-grow">
            <h2 class="font-bold text-teal-700 mb-1">เรียกเก็บในแชทส่วนตัว</h2>
          </div>
          <button
            class="bg-green-500 text-white py-2 px-4 rounded-md font-medium"
          >
            + สร้างบิล
          </button>
        </div>

        <!-- Bill List Section -->
        <div class="bg-white shadow-md rounded-lg p-4">
          <h3 class="font-bold text-gray-700 mb-2">บิลที่พี่สร้างไว้</h3>

          <div class="flex justify-between items-center border-t pt-4">
            <!-- Left Content -->
            <div>
              <h4 class="font-semibold text-gray-800">เข้ามาจ่ายได้เลย~</h4>
              <p class="text-sm text-gray-500">
                ฿1 | รอจ่าย 1 คน | วันที่ 03 ม.ค. 68
              </p>
            </div>

            <!-- Right Icon -->
            <div class="flex items-center">
              <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                <img
                  src="https://via.placeholder.com/40"
                  alt="Popcorn"
                  class="w-full h-full rounded-full"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
