<script setup>
// Mengimpor Link dari Inertia.js untuk navigasi halaman
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <div class="login-page">
        <!-- Bagian header merah -->
        <section class="login-hero">
            <!-- Mengatur seluruh konten identitas aplikasi -->
            <Link href="/" class="brand-content">
                <!-- Ilustrasi koki -->
                <img
                    src="/assets/images/illustrations/ilustrasi-koki.png"
                    alt="Ilustrasi Koki"
                    class="chef-image"
                />

                <!-- Judul sistem kasir -->
                <h1>
                    Sistem Kasir
                    <br />
                    UMKM Ayam Jagal
                </h1>

                <!-- Gambar ayam goreng -->
                <img
                    src="/assets/images/products/ayam-goreng.png"
                    alt="Ayam Goreng"
                    class="chicken-image"
                />
            </Link>
        </section>

        <!-- Bagian form login -->
        <main class="login-content">
            <!-- Tempat menampilkan form login dari halaman Login.vue -->
            <div class="login-card">
                <slot />
            </div>
        </main>
    </div>
</template>

<style scoped>
.login-page {
    width: 100%;
    min-height: 100vh;
    min-height: 100dvh;
    margin: 0 auto;
    background: #ca0708;
    overflow: hidden;
    font-family: Arial, Helvetica, sans-serif;
    display: flex;
    flex-direction: column;
}

.login-hero {
    width: 100%;
    height: clamp(300px, 48vh, 480px);
    flex-shrink: 0;
    background: #ca0708;
    position: relative;
    overflow: hidden;
}

.brand-content {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    position: relative;
}

.chef-image {
    width: clamp(95px, 13vw, 135px);
    height: clamp(85px, 12vw, 109px);
    object-fit: contain;
    margin-top: clamp(24px, 5vh, 45px);
    flex-shrink: 0;
}

.brand-content h1 {
    margin-top: 8px;
    color: #ffffff;
    font-size: clamp(19px, 3vw, 25px);
    line-height: 1.25;
    font-weight: 700;
    text-align: center;
}

.chicken-image {
    display: block;
    width: min(100%, 412px);
    height: clamp(170px, 27vh, 280px);
    object-fit: contain;
    flex-shrink: 0;
    margin-top: -15px;
    transform: translateY(-25px) scale(1.15);
    transform-origin: center top;
}

.login-content {
    width: 100%;
    flex: 1;
    min-height: 0;
    background: #ffffff;
    border-radius: 32px 32px 0 0;
    border: 2px solid #e5e5e5;
    border-bottom: none;
    margin-top: -1px;
    padding: clamp(22px, 4vh, 35px) clamp(20px, 5vw, 32px) 35px;
    position: relative;
    z-index: 2;
    box-sizing: border-box;
}

.login-card {
    width: 100%;
    max-width: 440px;
    margin: 0 auto;
}

@media (min-width: 768px) {
    .login-page {
        min-height: 100vh;
    }

    .login-hero {
        height: clamp(340px, 45vh, 480px);
    }

    .login-content {
        max-width: 560px;
        width: calc(100% - 48px);
        margin: -1px auto 30px;
        border-radius: 28px;
        border-bottom: 2px solid #e5e5e5;
        flex: 0 0 auto;
        min-height: 0;
        padding: 36px 42px;
    }
}

@media (max-width: 480px) {
    .login-hero {
        height: clamp(280px, 43vh, 400px);
    }

    .chicken-image {
        width: 100%;
        height: clamp(160px, 24vh, 220px);
        transform: translateY(-20px) scale(1.1);
    }

    .login-content {
        padding: 24px 22px 32px;
    }
}

@media (max-width: 360px) {
    .login-content {
        padding-left: 17px;
        padding-right: 17px;
    }

    .brand-content h1 {
        font-size: 19px;
    }
}

@media (max-height: 650px) {
    .login-hero {
        height: 280px;
    }

    .chef-image {
        margin-top: 18px;
    }

    .chicken-image {
        height: 160px;
    }
}

/* Penyesuaian visual mobile */
@media (max-width: 480px) {
    .login-hero {
        height: clamp(390px, 52vh, 470px);
    }

    .chef-image {
        width: 160px;
        height: 130px;
        margin-top: 30px;
    }

    .brand-content h1 {
        font-size: 24px;
        line-height: 1.25;
        margin-top: 4px;
    }

    .chicken-image {
        width: min(100%, 440px);
        height: 270px;
        margin-top: -5px;
        transform: translateY(-12px) scale(1.3);
    }

    .login-content {
        width: calc(100% - 20px);
        margin-left: auto;
        margin-right: auto;
        padding: 20px 18px 26px;
        border-radius: 28px 28px 0 0;
    }
}

@media (max-width: 360px) {
    .chef-image {
        width: 145px;
        height: 120px;
    }

    .chicken-image {
        height: 245px;
        transform: translateY(-10px) scale(1.2);
    }
}

/* Final responsive layout */
@media (max-width: 480px) {
    .login-page {
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
    }

    .login-hero {
        width: 100%;
        height: 510px;
        min-height: 510px;
        flex-shrink: 0;
        overflow: hidden;
    }

    .brand-content {
        width: 100%;
        height: 100%;
    }

    .chef-image {
        width: 155px;
        height: 125px;
        margin-top: 25px;
        flex-shrink: 0;
    }

    .brand-content h1 {
        margin-top: 5px;
        font-size: 24px;
        line-height: 1.25;
    }

    .chicken-image {
        display: block;
        width: 100%;
        max-width: 440px;
        height: 280px;
        object-fit: contain;
        margin-top: -5px;
        transform: translateY(0) scale(1.20);
        transform-origin: center center;
        flex-shrink: 0;
    }

    .login-content {
        width: 100%;
        max-width: 100%;
        min-width: 0;
        flex: 1;
        min-height: 0;
        margin: -1px 0 0;
        padding: 22px 22px 32px;
        box-sizing: border-box;
        border-radius: 28px 28px 0 0;
        border: 2px solid #e5e5e5;
        border-bottom: none;
        background: #ffffff;
    }

    .login-card {
        width: 100%;
        max-width: 440px;
        margin: 0 auto;
    }
}

@media (max-width: 360px) {
    .login-hero {
        height: 485px;
        min-height: 485px;
    }

    .chef-image {
        width: 140px;
        height: 115px;
    }

    .brand-content h1 {
        font-size: 22px;
    }

    .chicken-image {
        height: 265px;
        transform: scale(1.15);
    }

    .login-content {
        padding-left: 18px;
        padding-right: 18px;
    }
}

@media (min-width: 768px) {
    .login-content {
        width: calc(100% - 48px);
        max-width: 560px;
        margin: -1px auto 30px;
        flex: 0 0 auto;
        border-radius: 28px;
        border-bottom: 2px solid #e5e5e5;
    }
}
</style>
