<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";

const form = useForm({
    password: "",
});

const submit = () => {
    form.post(route("password.confirm"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Konfirmasi Password - Sistem Kasir Ayam Jagal" />

        <div class="login-form">
            <div class="welcome-heading">
                <h2>Konfirmasi Password</h2>

                <p>
                    Silakan masukkan password akun Anda
                    untuk melanjutkan.
                </p>
            </div>

            <form @submit.prevent="submit">
                <div class="form-group password-group">
                    <label for="password">Password:</label>

                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        autocomplete="current-password"
                        required
                        autofocus
                        class="login-input"
                        placeholder="Masukkan password"
                    />

                    <InputError
                        class="mt-1"
                        :message="form.errors.password"
                    />
                </div>

                <button
                    type="submit"
                    class="login-button"
                    :disabled="form.processing"
                >
                    {{ form.processing ? "Memproses..." : "Konfirmasi" }}
                </button>
            </form>
        </div>
    </GuestLayout>
</template>
