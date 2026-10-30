<script setup>
import { ref } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const showPassword = ref(false);

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

// Mengirim data login ke backend Laravel
const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Login - Sistem Kasir Ayam Jagal" />

        <div class="login-form">
            <!-- Menampilkan pesan status -->
            <div
                v-if="status"
                class="mb-4 rounded-lg bg-green-50 p-3 text-sm text-green-700"
            >
                {{ status }}
            </div>

            <!-- Judul selamat datang -->
            <div class="welcome-heading">
                <h2>Selamat Datang</h2>

                <p>
                    Login untuk mengakses sistem kasir
                    <br />
                    UMKM Ayam Jagal
                </p>
            </div>

            <form @submit.prevent="submit">
                <!-- Input username -->
                <div class="form-group username-group">
                    <label for="email">Username:</label>

                    <input
                        id="email"
                        v-model="form.email"
                        type="text"
                        autocomplete="username"
                        required
                        autofocus
                        class="login-input"
                    />

                    <InputError class="mt-1" :message="form.errors.email" />
                </div>

                <!-- Input password -->
                <div class="form-group password-group">
                    <label for="password">Password:</label>

                    <div class="password-wrapper">
                        <input
                            id="password"
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            autocomplete="current-password"
                            required
                            class="login-input"
                        />

                        <button
                            type="button"
                            class="password-toggle"
                            @click="showPassword = !showPassword"
                            :aria-label="
                                showPassword
                                    ? 'Sembunyikan password'
                                    : 'Tampilkan password'
                            "
                        >
                            <svg
                                v-if="!showPassword"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                            >
                                <path
                                    d="M12 5C6.5 5 2.2 8.4 1 12c1.2 3.6 5.5 7 11 7s9.8-3.4 11-7c-1.2-3.6-5.5-7-11-7Zm0 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8Zm0-2a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"
                                />
                            </svg>

                            <svg v-else viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M2 3.4 3.4 2 22 20.6 20.6 22l-3.1-3.1c-1.7.8-3.6 1.1-5.5 1.1-5.5 0-9.8-3.4-11-7 0-1.7 1.6-3.7 4-5.2L2 3.4Zm10 1.6c5.5 0 9.8 3.4 11 7-.5 1.5-1.5 2.9-2.9 4l-3.2-3.2a4 4 0 0 0-5.4-5.4L9.2 5.1c.9-.1 1.8-.1 2.8-.1Z"
                                />
                            </svg>
                        </button>
                    </div>

                    <InputError class="mt-1" :message="form.errors.password" />
                </div>

                <!-- Checkbox dan lupa password -->
                <div class="form-options">
                    <label class="remember-label">
                        <input
                            type="checkbox"
                            v-model="form.remember"
                            class="remember-checkbox"
                        />

                        <span>Ingat saya</span>
                    </label>

                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="forgot-link"
                    >
                        Lupa Password ?
                    </Link>
                </div>

                <!-- Tombol login -->
                <button
                    type="submit"
                    class="login-button"
                    :disabled="form.processing"
                >
                    {{ form.processing ? "Memproses..." : "Login" }}
                </button>
            </form>
        </div>
    </GuestLayout>
</template>

<style scoped>
/* Mengatur keseluruhan form */
.login-form {
    width: 100%;
}

/* Mengatur judul selamat datang */
.welcome-heading {
    margin-bottom: 24px;
}

.welcome-heading h2 {
    font-size: 25px;
    line-height: 31px;
    font-weight: 700;
    color: #111111;
    margin: 0;
}

.welcome-heading p {
    font-size: 12px;
    line-height: 16px;
    color: #929292;
    margin-top: 2px;
}

/* Mengatur kelompok input */
.form-group {
    width: 100%;
}

.form-group label {
    display: block;
    color: #111111;
    font-size: 12px;
    margin: 0 0 5px 6px;
}

.username-group {
    margin-bottom: 20px;
}

.password-group {
    margin-bottom: 18px;
}

/* Mengatur kotak input */
.login-input {
    width: 100%;
    height: 36px;
    border: 1px solid #c9c9c9;
    border-radius: 9px;
    background: #ffffff;
    padding: 0 12px;
    font-size: 14px;
    color: #222222;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.18);
    outline: none;
    transition:
        border-color 0.2s,
        box-shadow 0.2s;
}

.login-input:focus {
    border-color: #ca0708;
    box-shadow: 0 0 0 2px rgba(202, 7, 8, 0.1);
}

/* Mengatur tombol tampil password */
.password-wrapper {
    position: relative;
}

.password-wrapper .login-input {
    padding-right: 40px;
}

.password-toggle {
    position: absolute;
    right: 11px;
    top: 50%;
    transform: translateY(-50%);
    width: 22px;
    height: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: none;
    color: #000000;
    cursor: pointer;
    padding: 0;
}

.password-toggle svg {
    width: 20px;
    height: 20px;
}

/* Mengatur checkbox dan lupa password */
.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 7px;
    margin-top: 18px;
}

.remember-label {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 12px;
    color: #666666;
    cursor: pointer;
}

/* Mengatur checkbox merah */
.remember-checkbox {
    appearance: none;
    width: 22px;
    height: 22px;
    border: 1px solid #cccccc;
    border-radius: 6px;
    background: #ffffff;
    cursor: pointer;
    position: relative;
}

.remember-checkbox:checked {
    background: #ca0708;
    border-color: #ca0708;
}

.remember-checkbox:checked::after {
    content: "";
    position: absolute;
    left: 7px;
    top: 2px;
    width: 5px;
    height: 11px;
    border: solid #ffffff;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

/* Mengatur tautan lupa password */
.forgot-link {
    color: #ca0708;
    font-size: 12px;
    font-weight: 500;
    text-decoration: none;
}

.forgot-link:hover {
    text-decoration: underline;
}

/* Mengatur tombol login */
.login-button {
    width: 78%;
    height: 38px;
    display: block;
    margin: 22px auto 0;
    border: none;
    border-radius: 9px;
    background: linear-gradient(180deg, #ff0909 0%, #d90000 100%);
    color: #ffffff;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 3px 4px rgba(0, 0, 0, 0.25);
    transition:
        transform 0.2s,
        opacity 0.2s;
}

.login-button:hover {
    transform: translateY(-1px);
}

.login-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Menyesuaikan form pada HP kecil */
@media (max-width: 380px) {
    .welcome-heading h2 {
        font-size: 23px;
    }

    .login-button {
        width: 85%;
    }
}
</style>
