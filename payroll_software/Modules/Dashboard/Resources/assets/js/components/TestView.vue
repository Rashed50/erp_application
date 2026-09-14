<template>
    <div class="relative min-h-[300px] w-full bg-gray-50 p-4 rounded-lg shadow-inner">

        <div class="absolute inset-0 flex flex-col items-center justify-center bg-white/80 backdrop-blur-sm z-10 transition-opacity duration-300">

            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>

            <p class="text-sm font-medium text-gray-700 tracking-wide animate-pulse">
                Loading Dashboard...
            </p>
        </div>

        <div class="opacity-10">
            <p class="text-gray-800">This is a test view to verify the dashboard working correctly.</p>
        </div>
    </div>
</template>
