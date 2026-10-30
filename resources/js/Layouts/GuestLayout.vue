<script setup>
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <div class="login-page">
        <!-- Bagian header merah -->
        <section class="login-hero">
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
            <div class="login-card">
                <slot />
            </div>
        </main>
    </div>
</template>

<style scoped>
/* Mengatur halaman utama */
.login-page {
    width: 100%;
    max-width: 412px;
    min-height: 917px;
    margin: 0 auto;
    background: #ca0708;
    overflow: hidden;
    font-family: Arial, Helvetica, sans-serif;
}

/* Mengatur bagian header merah */
.login-hero {
    width: 100%;
    height: 480px;
    background: #ca0708;
    position: relative;
    overflow: hidden;
}

/* Mengatur susunan elemen header */
.brand-content {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    position: relative;
}

/* Mengatur ilustrasi koki */
.chef-image {
    width: 135px;
    height: 109px;
    object-fit: contain;
    margin-top: 45px;
    flex-shrink: 0;
}

/* Mengatur judul sistem kasir */
.brand-content h1 {
    margin-top: 8px;
    color: #ffffff;
    font-size: 23px;
    line-height: 29px;
    font-weight: 700;
    text-align: center;
}

/* Mengatur gambar ayam goreng */
.chicken-image {
    display: block;
    width: 412px;
    height: 280px;
    max-width: none;
    object-fit: contain;
    flex-shrink: 0;
    margin-top: -5px;
    transform: translateY(-25px) scale(1.3);
    transform-origin: center top;
}

/* Mengatur area form putih */
.login-content {
    width: 100%;
    min-height: 437px;
    background: #ffffff;
    border-radius: 36px 36px 0 0;
    border-top: 2px solid #e5e5e5;
    margin-top: -1px;
    padding: 25px 28px 35px;
    position: relative;
    z-index: 2;
}

/* Mengatur lebar konten form */
.login-card {
    width: 100%;
    max-width: 500px;
    margin: 0 auto;
}

/* Mengatur tampilan desktop */
@media (min-width: 768px) {
    .login-page {
        width: 412px;
        min-height: 917px;
    }

    .login-hero {
        width: 412px;
        height: 480px;
    }

    .login-content {
        width: 412px;
        min-height: 437px;
    }
}

/* Menyesuaikan tampilan HP kecil */
@media (max-width: 380px) {
    .login-page {
        width: 100%;
    }

    .login-hero {
        height: 450px;
        overflow: hidden;
    }

    .chef-image {
        width: 120px;
        height: 100px;
    }

    /* Mempertahankan ukuran ayam dan menaikkan posisinya */
    .chicken-image {
        width: 120%;
        height: 260px;
        object-fit: contain;
        transform: translateY(-55px) scale(1.2);
        transform-origin: center top;
    }

    .login-content {
        padding-left: 22px;
        padding-right: 22px;
    }
}
</style>
