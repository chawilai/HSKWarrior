<template>
    <div class="min-h-screen bg-teal-300">
      <!-- Header -->
      <div class="text-center py-4 bg-white shadow">
        <h1 class="text-lg font-bold">KhunThong</h1>
        <p class="text-sm text-gray-500">khunthong.kasikornbank.com</p>
      </div>

      <!-- Profile Section -->
      <div class="text-center mt-6">
        <!-- Confetti Background -->
        <div class="relative">
          <div class="absolute inset-0 flex justify-center">
            <div class="w-full h-16 bg-[url('https://via.assets.so/img.jpg?w=300&h=50&bg=red')] bg-no-repeat bg-center"></div>
          </div>
          <!-- Profile Image -->
          <img
            src="https://via.placeholder.com/100"
            alt="Profile"
            class="w-24 h-24 rounded-full mx-auto border-4 border-teal-300"
          />
        </div>
        <h2 class="text-xl font-bold mt-2">Whattttt</h2>
      </div>

      <!-- Badge Section -->
      <div class="flex justify-center mt-6 space-x-4">
        <!-- Badge 1 -->
        <div class="flex flex-col items-center">
          <div class="bg-teal-400 rounded-full w-12 h-12 flex items-center justify-center">
            <img
              src="https://via.assets.so/img.jpg?w=50&h=50&tc=yellow"
              alt="Crown"
              class="w-6"
            />
          </div>
          <p class="text-sm text-gray-900 font-bold">0</p>
        </div>
        <!-- Badge 2 -->
        <div class="flex flex-col items-center">
          <div class="bg-yellow-400 rounded-full w-12 h-12 flex items-center justify-center">
            <img
              src="https://via.assets.so/img.jpg?w=50&h=50&tc=gold"
              alt="Gold Medal"
              class="w-6"
            />
          </div>
          <p class="text-sm text-gray-900 font-bold">0</p>
        </div>
        <!-- Badge 3 -->
        <div class="flex flex-col items-center">
          <div class="bg-gray-400 rounded-full w-12 h-12 flex items-center justify-center">
            <img
              src="https://via.assets.so/img.jpg?w=50&h=50&tc=silver"
              alt="Silver Medal"
              class="w-6"
            />
          </div>
          <p class="text-sm text-gray-900 font-bold">0</p>
        </div>
        <!-- Badge 4 -->
        <div class="flex flex-col items-center">
          <div class="bg-orange-400 rounded-full w-12 h-12 flex items-center justify-center">
            <img
              src="https://via.assets.so/img.jpg?w=50&h=50&tc=bronze"
              alt="Bronze Medal"
              class="w-6"
            />
          </div>
          <p class="text-sm text-gray-900 font-bold">0</p>
        </div>
      </div>

      <!-- Tabs Section -->
      <div class="flex justify-around mt-6 bg-teal-400 text-white py-2">
        <button class="font-bold underline">กิจกรรม</button>
        <button class="font-bold">คูปองของฉัน</button>
      </div>

      <!-- Placeholder Section -->
      <div class="flex flex-col items-center mt-8">
        <img
          src="https://via.assets.so/img.jpg?w=100&h=100&bg=yellow"
          alt="Placeholder"
          class="w-24"
        />
        <p class="text-gray-700 text-sm mt-2">
          ช่วงนี้ยังไม่มีกิจกรรมอะไรเลยค้าบ~ ~
        </p>
      </div>
    </div>
  </template>
