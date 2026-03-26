<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            // Kategori 1: Autentikasi & Keamanan (OTP)
            ['category' => 'Authentication', 'name' => 'OTP Login', 'content' => 'Kode verifikasi Anda adalah {{code}}. Jangan berikan kode ini kepada siapa pun termasuk pihak {{bisnis}}. Kode berlaku 5 menit.'],
            ['category' => 'Authentication', 'name' => 'Reset Password', 'content' => 'Halo, kami menerima permintaan reset password. Gunakan link berikut untuk melanjutkan: {{link}}. Jika bukan Anda, segera amankan akun Anda.'],
            ['category' => 'Authentication', 'name' => 'Pendaftaran Berhasil', 'content' => 'Selamat! Akun Anda di {{bisnis}} telah aktif. Silakan login untuk mulai menikmati layanan kami.'],

            // Kategori 2: Transaksi & E-commerce
            ['category' => 'Commerce', 'name' => 'Konfirmasi Pesanan', 'content' => 'Halo {{nama}}, pesanan #{{order_id}} telah kami terima. Kami akan segera memprosesnya. Terima kasih!'],
            ['category' => 'Commerce', 'name' => 'Update Pengiriman', 'content' => 'Paket Anda #{{order_id}} sedang dikirim oleh kurir {{ekspedisi}}. Lacak posisi paket di: {{link_resi}}.'],
            ['category' => 'Commerce', 'name' => 'Pesanan Selesai', 'content' => 'Paket telah diterima! Terima kasih sudah berbelanja di {{bisnis}}. Berikan ulasan Anda di sini: {{link}}.'],
            ['category' => 'Commerce', 'name' => 'Pengingat Pembayaran', 'content' => 'Halo {{nama}}, segera selesaikan pembayaran pesanan #{{order_id}} sebelum pukul {{jam}} agar pesanan tidak otomatis dibatalkan.'],
            ['category' => 'Commerce', 'name' => 'Stok Kembali Tersedia', 'content' => 'Barang impianmu {{produk}} sudah ready kembali! Stok terbatas, sikat sekarang di: {{link}}.'],

            // Kategori 3: Customer Service & Billing
            ['category' => 'Service & Billing', 'name' => 'Salam Pembuka', 'content' => 'Halo! Terima kasih telah menghubungi {{bisnis}}. Pesan Anda telah kami terima dan akan segera dibalas oleh tim kami.'],
            ['category' => 'Service & Billing', 'name' => 'Info Tagihan', 'content' => 'Tagihan bulan {{bulan}} sebesar Rp {{nominal}} sudah terbit. Batas akhir pembayaran: {{tanggal}}.'],
            ['category' => 'Service & Billing', 'name' => 'Konfirmasi Pembayaran', 'content' => 'Terima kasih! Pembayaran sebesar Rp {{nominal}} telah kami terima. Status layanan Anda kini aktif.'],
            ['category' => 'Service & Billing', 'name' => 'Tiket Support', 'content' => 'Tiket bantuan Anda telah dibuat: #{{ticket_id}}. Tim kami akan merespons dalam waktu maksimal 24 jam.'],

            // Kategori 4: Reservasi & Janji Temu
            ['category' => 'Reservation', 'name' => 'Konfirmasi Booking', 'content' => 'Reservasi Anda di {{tempat}} untuk tanggal {{tanggal}} pukul {{jam}} telah berhasil dikonfirmasi.'],
            ['category' => 'Reservation', 'name' => 'Reminder Janji Temu', 'content' => 'Halo {{nama}}, mengingatkan janji temu Anda besok pukul {{jam}}. Mohon datang 15 menit lebih awal.'],
            ['category' => 'Reservation', 'name' => 'Pembatalan Janji', 'content' => 'Mohon maaf, janji temu Anda pada {{tanggal}} telah dibatalkan. Hubungi kami untuk menjadwalkan ulang.'],

            // Kategori 5: Marketing & Promosi
            ['category' => 'Marketing', 'name' => 'Diskon Spesial', 'content' => 'Khusus buat {{nama}}! Dapatkan diskon 50% untuk semua produk hanya hari ini. Gunakan kode: PROMO50.'],
            ['category' => 'Marketing', 'name' => 'Undangan Event', 'content' => 'Halo! Kami mengundang Anda untuk hadir di acara {{event}} pada {{tanggal}}. Daftar gratis di: {{link}}.'],
            ['category' => 'Marketing', 'name' => 'Flash Sale', 'content' => 'Sisa 2 jam lagi! Flash sale {{produk}} sedang berlangsung. Jangan sampai kehabisan!'],
            ['category' => 'Marketing', 'name' => 'Voucher Ulang Tahun', 'content' => 'Selamat ulang tahun {{nama}}! Ini kado spesial buat kamu: Voucher potongan Rp 100rb. Pakai di: {{link}}.'],

            // Kategori 6: Pengingat & Notifikasi (Utility)
            ['category' => 'Utility', 'name' => 'Layanan Akan Berakhir', 'content' => 'Masa aktif paket {{layanan}} Anda akan habis dalam 3 hari. Perpanjang sekarang agar tetap bisa digunakan.'],
            ['category' => 'Utility', 'name' => 'Update Kebijakan', 'content' => 'Kami melakukan pembaruan pada Syarat & Ketentuan. Silakan baca perubahannya di: {{link}}.'],
            ['category' => 'Utility', 'name' => 'Pemberitahuan Maintenance', 'content' => 'Halo, sistem kami akan melakukan maintenance pada pukul {{jam}} WIB. Mohon maaf atas ketidaknyamanannya.'],

            // Kategori 7: Pendidikan & Sekolah
            ['category' => 'Education', 'name' => 'Absensi Siswa', 'content' => 'Info Sekolah: Siswa {{nama}} telah hadir di sekolah pada pukul {{jam}}.'],
            ['category' => 'Education', 'name' => 'Pengumuman Nilai', 'content' => 'Hasil ujian {{mapel}} sudah keluar. Silakan cek detail nilai Anda di portal siswa: {{link}}.'],
            ['category' => 'Education', 'name' => 'Pengingat Tugas', 'content' => 'Halo {{nama}}, jangan lupa kumpulkan tugas {{mapel}} paling lambat hari ini pukul 23.59.'],

            // Kategori 8: HRD & Rekrutmen
            ['category' => 'HR & Recruitment', 'name' => 'Panggilan Interview', 'content' => 'Halo {{nama}}, kami mengundang Anda untuk interview posisi {{posisi}} pada {{tanggal}} pukul {{jam}}.'],
            ['category' => 'HR & Recruitment', 'name' => 'Update Lamaran', 'content' => 'Terima kasih telah melamar. Saat ini lamaran Anda sedang dalam tahap review oleh tim rekrutmen kami.'],

            // Kategori 9: Feedback & Survei
            ['category' => 'Feedback', 'name' => 'Survei Kepuasan', 'content' => 'Bagaimana pengalaman Anda menggunakan {{bisnis}} hari ini? Berikan nilai Anda di sini: {{link}}.'],
            ['category' => 'Feedback', 'name' => 'Testimonial', 'content' => 'Halo {{nama}}, kami senang Anda puas! Bolehkah kami meminta 1 menit waktu Anda untuk mengisi ulasan?'],

            // Kategori 10: Ucapan Hari Raya
            ['category' => 'Seasonal', 'name' => 'Ucapan Hari Raya', 'content' => 'Keluarga besar {{bisnis}} mengucapkan Selamat Idul Fitri 144x H. Mohon maaf lahir dan batin.'],

            // Kategori 11: Kesehatan & Medis
            ['category' => 'Healthcare', 'name' => 'Hasil Lab', 'content' => 'Halo {{nama}}, hasil pemeriksaan lab Anda sudah tersedia. Silakan cek melalui aplikasi atau klik link berikut: {{link}}.'],
            ['category' => 'Healthcare', 'name' => 'Pengambilan Obat', 'content' => 'Resep obat Anda telah siap diambil di Apotek {{nama_apotek}}. Jangan lupa membawa kartu identitas.'],
            ['category' => 'Healthcare', 'name' => 'Pendaftaran Pasien Baru', 'content' => 'Terima kasih telah mendaftar di RS {{rs_name}}. Nomor rekam medis Anda adalah {{no_rm}}.'],
            ['category' => 'Healthcare', 'name' => 'Jadwal Dokter', 'content' => 'Dr. {{nama_dokter}} akan memulai praktik pukul {{jam}}. Mohon konfirmasi kedatangan Anda.'],
            ['category' => 'Healthcare', 'name' => 'Vaksinasi', 'content' => 'Jadwal vaksinasi dosis ke-{{dosis}} Anda adalah tanggal {{tanggal}} di {{lokasi}}.'],

            // Kategori 12: Properti & Real Estate
            ['category' => 'Real Estate', 'name' => 'Jadwal Survey', 'content' => 'Halo, konfirmasi kunjungan unit di {{proyek}} untuk besok jam {{jam}} dengan agen {{nama_agen}}.'],
            ['category' => 'Real Estate', 'name' => 'Pembaruan Sewa', 'content' => 'Masa sewa properti Anda di {{lokasi}} akan berakhir dalam 30 hari. Apakah Anda ingin memperpanjang?'],
            ['category' => 'Real Estate', 'name' => 'Tagihan Maintenance', 'content' => 'Tagihan pemeliharaan lingkungan (IPL) unit {{no_unit}} bulan ini sebesar Rp {{nominal}} telah terbit.'],
            ['category' => 'Real Estate', 'name' => 'Ketersediaan Unit', 'content' => 'Unit idaman Anda di {{proyek}} tersedia kembali! Hubungi kami segera sebelum terjual.'],

            // Kategori 13: Keuangan & Fintech
            ['category' => 'Finance', 'name' => 'Notifikasi Transaksi', 'content' => 'Debet sebesar Rp {{nominal}} dari rekening {{no_rek}} untuk {{keterangan}} pada {{jam}}.'],
            ['category' => 'Finance', 'name' => 'Persetujuan Pinjaman', 'content' => 'Selamat! Pengajuan pinjaman Anda senilai Rp {{nominal}} telah disetujui. Dana akan cair dalam 1x24 jam.'],
            ['category' => 'Finance', 'name' => 'Gagal Bayar (Urgent)', 'content' => 'Peringatan: Cicilan Anda sudah lewat jatuh tempo. Hindari denda tambahan dengan membayar hari ini.'],
            ['category' => 'Finance', 'name' => 'Verifikasi Wajah', 'content' => 'Silakan lakukan verifikasi wajah melalui link ini untuk meningkatkan limit akun Anda: {{link}}.'],

            // Kategori 14: Restoran & Food Delivery
            ['category' => 'F&B', 'name' => 'Pesanan Siap Diantar', 'content' => 'Kabar gembira! Pesanan {{menu}} kamu sudah dibawa oleh kurir dan akan sampai dalam {{menit}} menit.'],
            ['category' => 'F&B', 'name' => 'Meja Tersedia', 'content' => 'Meja untuk {{pax}} orang atas nama {{nama}} sudah siap. Kami tunggu kedapatannya di {{restoran}}!'],
            ['category' => 'F&B', 'name' => 'Review Makanan', 'content' => 'Gimana rasa {{menu}} tadi? Kasih rating yuk buat dapet voucher gratis: {{link}}.'],
            ['category' => 'F&B', 'name' => 'Menu Spesial', 'content' => 'Menu favoritmu {{menu}} lagi diskon cuma hari ini! Pesan lewat WA sekarang.'],

            // Kategori 15: Logistik & Ekspedisi
            ['category' => 'Logistics', 'name' => 'Gagal Kirim', 'content' => 'Kurir kami gagal mengantar paket #{{resi}} karena rumah kosong. Kami akan coba kirim kembali besok.'],
            ['category' => 'Logistics', 'name' => 'Pick-up Driver', 'content' => 'Driver {{nama_driver}} sedang menuju lokasi Anda untuk mengambil paket.'],
            ['category' => 'Logistics', 'name' => 'Paket di Drop Point', 'content' => 'Paket #{{resi}} sudah sampai di Drop Point {{nama_agen}}. Silakan ambil sebelum pukul 20.00.'],

            // Kategori 16: Keanggotaan & Fitness
            ['category' => 'Fitness', 'name' => 'Membership Expired', 'content' => 'Masa member gym Anda habis besok. Perpanjang hari ini dan dapatkan bonus 1 bulan gratis!'],
            ['category' => 'Fitness', 'name' => 'Booking Kelas', 'content' => 'Konfirmasi: Anda telah terdaftar di kelas {{nama_kelas}} hari ini pukul {{jam}}.'],
            ['category' => 'Fitness', 'name' => 'Personal Trainer', 'content' => 'Halo {{nama}}, Coach {{trainer}} sudah menunggu untuk sesi latihan jam {{jam}}.'],

            // Kategori 17: Tiketing & Event
            ['category' => 'Event', 'name' => 'E-Ticket', 'content' => 'Ini E-Ticket Anda untuk {{event}}. Tunjukkan QR Code ini di gerbang masuk: {{link_qr}}.'],
            ['category' => 'Event', 'name' => 'Perubahan Jadwal', 'content' => 'Penting! Acara {{event}} yang seharusnya tanggal {{tgl_lama}} diundur menjadi {{tgl_baru}}.'],
            ['category' => 'Event', 'name' => 'Open Gate', 'content' => 'Gerbang untuk {{event}} sudah dibuka! Harap siapkan tiket Anda dan selamat bersenang-senang.'],

            // Kategori 18: Otomotif
            ['category' => 'Automotive', 'name' => 'Servis Berkala', 'content' => 'Waktunya servis rutin untuk kendaraan {{no_plat}}. Booking sekarang untuk menghindari antrean.'],
            ['category' => 'Automotive', 'name' => 'Suku Cadang Tersedia', 'content' => 'Part {{sparepart}} yang Anda pesan sudah tiba di bengkel. Silakan lakukan pemasangan.'],
            ['category' => 'Automotive', 'name' => 'Reminder Asuransi', 'content' => 'Asuransi kendaraan Anda akan habis pada {{tanggal}}. Lindungi kendaraan Anda kembali di: {{link}}.'],

            // Kategori 19: Notifikasi Keamanan
            ['category' => 'Security Info', 'name' => 'Login Baru', 'content' => 'Akun Anda diakses dari perangkat baru di lokasi {{lokasi}}. Jika bukan Anda, segera ganti password.'],
            ['category' => 'Security Info', 'name' => 'Perubahan Email', 'content' => 'Email akun Anda telah diubah menjadi {{email_baru}}. Klik di sini jika Anda tidak melakukan ini.'],
            ['category' => 'Security Info', 'name' => 'Akses API', 'content' => 'API Key baru telah dibuat untuk aplikasi {{app_name}}. Pastikan Anda menyimpannya dengan aman.'],

            // Kategori 20: Template Ramah Tamah
            ['category' => 'Personalized', 'name' => 'Terima Kasih Kunjungan', 'content' => 'Senang bertemu Anda hari ini di {{toko}}. Semoga produk kami memuaskan!'],
            ['category' => 'Personalized', 'name' => 'Follow Up Sales', 'content' => 'Halo {{nama}}, saya {{sales_name}} dari {{bisnis}}. Apakah ada yang bisa saya bantu terkait produk kemarin?'],
            ['category' => 'Personalized', 'name' => 'Survey Singkat', 'content' => 'Bantu kami jadi lebih baik! Isi survei 30 detik ini dan dapatkan saldo e-wallet: {{link}}.'],
        ];

        foreach ($templates as $tpl) {
            Template::create([
                'user_id' => null,
                'is_system' => true,
                'name' => $tpl['name'],
                'category' => $tpl['category'],
                'content' => $tpl['content'],
                'is_active' => true,
            ]);
        }
    }
}
