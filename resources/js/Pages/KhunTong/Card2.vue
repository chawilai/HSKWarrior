<template>
    <div class="min-h-screen bg-gray-100 p-4">
      <!-- Header Section -->
      <div class="flex justify-between items-center mb-4">
        <span class="text-sm font-medium text-gray-600">Private Bill</span>
        <img src="https://via.placeholder.com/30" alt="KBANK Logo" class="w-6 h-6" />
      </div>

      <!-- Bill Amount -->
      <div class="bg-white shadow-md rounded-lg p-4">
        <h1 class="text-2xl font-bold text-green-600">฿1 บาท</h1>
        <p class="text-sm text-gray-500 mt-1">“คืนเงินด้วยจ้า~” จาก <span class="font-semibold">@toeytoey</span></p>
        <p class="text-xs text-gray-400 mt-1">
          โปรดตรวจสอบรายละเอียดบิลก่อน กดจ่ายเงิน<br />
          03 ม.ค. 68, 11:55 น.
        </p>
      </div>

      <!-- Recipient Information -->
      <div class="bg-white shadow-md rounded-lg p-4 mt-4">
        <h2 class="font-semibold text-gray-700 mb-2">บัญชีรับเงิน</h2>
        <div class="flex items-center">
          <img src="https://via.placeholder.com/40" alt="PromptPay Logo" class="w-10 h-10 rounded-full" />
          <div class="ml-4">
            <p class="font-medium text-gray-800">ปภาวี วิริยะเสนา</p>
            <p class="text-sm text-gray-500">เลขบัญชี: 091-8-53377-1</p>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex justify-between mt-4">
        <button
          class="bg-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium"
        >
          ไม่ใช่บิลของฉัน
        </button>
        <button
          class="bg-green-500 text-white px-6 py-2 rounded-lg text-sm font-medium"
        >
          จ่ายเงิน
        </button>
      </div>
    </div>
  </template>
