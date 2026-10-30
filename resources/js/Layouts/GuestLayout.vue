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
/* Mengatur halaman utama */
.login-page {
    width: 100%;
    max-width: 412px;
    min-height: 100vh;
    min-height: 100dvh;
    margin: 0 auto;

    /* Background merah agar terlihat pada sisi lengkungan card */
    background: #ca0708;

    overflow: hidden;
    font-family: Arial, Helvetica, sans-serif;

    /* Menyusun header dan form secara vertikal */
    display: flex;
    flex-direction: column;
}

/* Mengatur bagian header merah */
.login-hero {
    width: 100%;
    height: 480px;
    flex-shrink: 0;
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

    /* Mengatur jarak gambar ayam dari judul */
    margin-top: -15px;

    /* Menurunkan gambar agar berada lebih ke tengah area merah */
    transform: translateY(-45px) scale(1.3);

    /* Menentukan titik awal transformasi */
    transform-origin: center top;
}

/* Mengatur area form putih */
.login-content {
    width: 100%;

    /* Mengisi seluruh sisa tinggi halaman */
    flex: 1;

    /* Memastikan area putih tidak mengecil */
    min-height: 437px;

    /* Bagian dalam card tetap putih */
    background: #ffffff;

    /* Membuat lengkungan lebih kecil seperti desain Figma */
    border-radius: 32px 32px 0 0;

    /* Memberikan garis tepi abu-abu yang halus */
    border: 2px solid #e5e5e5;
    border-bottom: none;

    margin-top: -1px;
    padding: 25px 28px 35px;

    position: relative;
    z-index: 2;
    box-sizing: border-box;
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
        min-height: 100vh;
        min-height: 100dvh;
    }

    .login-hero {
        width: 412px;
        height: 480px;
    }

    .login-content {
        width: 412px;
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

    /* Menyesuaikan ukuran ilustrasi koki */
    .chef-image {
        width: 120px;
        height: 100px;
    }

    /* Mengatur ukuran dan posisi ayam pada HP kecil */
    .chicken-image {
        width: 120%;
        height: 260px;
        object-fit: contain;

        /* Menyesuaikan posisi ayam */
        transform: translateY(-45px) scale(1.2);
        transform-origin: center top;
    }

    /* Menyesuaikan jarak form dari sisi layar */
    .login-content {
        padding-left: 22px;
        padding-right: 22px;
    }
}
</style>
