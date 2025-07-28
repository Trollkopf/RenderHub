<script setup>
import { Head, Link } from '@inertiajs/vue3'
</script>

<template>

    <Head title="Renderhub - Welcome" />

    <div class="min-h-screen bg-[#0f172a] text-white flex items-center justify-center px-4">
        <div class="max-w-xl w-full text-center">

            <!-- Logo -->
            <div class="flex justify-center mb-8">
                <img src="/renderhub_logo.png" alt="Renderhub Logo" class="h-20 w-20" />
            </div>

            <h1 class="text-4xl font-bold mb-4">Bienvenido a Renderhub</h1>
            <p class="text-lg text-white/70 mb-8">
                Comienza a gestionar tus proyectos, clientes y archivos de forma eficiente con nuestra plataforma
                integral.
            </p>

            <div class="flex justify-center gap-4">
                <Link :href="route('login')"
                    class="px-6 py-3 bg-white/10 hover:bg-white/20 rounded-lg font-medium text-white transition">
                Log in
                </Link>

                <Link :href="route('register')"
                    class="px-6 py-3 bg-teal-600 hover:bg-teal-700 rounded-lg font-medium text-white transition">
                Register
                </Link>
            </div>
        </div>
    </div>
</template>

<style scoped>
body {
    font-family: 'Inter', sans-serif;
}
</style>
