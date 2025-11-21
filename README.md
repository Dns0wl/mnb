# HW DNS Manual

Plugin WordPress ringan untuk membuat dan mencetak Manual Paper berformat A5 dengan latar belakang khusus. Admin dapat mengisi data pelanggan, tanggal pembelian, serta kanal pembelian, kemudian melihat atau mengunduh PDF hasilnya.

## Fitur
- Halaman dashboard **Manual Paper** dengan tabel daftar manual.
- Tombol **Add New Manual** memunculkan modal "Manual Data" berisi input wajib:
  - Customer Name
  - Purchase Date (pilih **Now** atau gunakan kalender **Choose** untuk format DD/MM/YY)
  - Purchase Channel dengan opsi Marketplace, Customer Service, Boutique, Website
  - Opsi lanjutan untuk Marketplace: Tiktok Shop, Shopee, atau Tokopedia
- Aksi **Submit** membuat entri Manual Paper dan menyediakan tombol **View** serta **Download** PDF.
- PDF A5, font 12, memakai latar belakang yang sama untuk setiap manual: `https://hayuwidyas.com/wp-content/uploads/2025/11/MNB-L_Cust_HW-BG.png`.

## Instalasi
1. Salin folder plugin ke direktori `wp-content/plugins/hw-dns-manual` pada situs WordPress Anda.
2. Aktifkan plugin **HW DNS Manual** melalui menu **Plugins** di dashboard.

## Penggunaan
1. Buka menu **Manual Paper** di dashboard.
2. Klik **Add New Manual** dan lengkapi seluruh field pada modal.
3. Tekan **Submit** untuk membuat manual dan otomatis menyiapkan PDF.
4. Gunakan tombol **View** untuk membuka PDF di browser atau **Download** untuk mengunduhnya.

## Catatan
- Plugin menggunakan ekstensi PHP **Imagick** untuk membangkitkan PDF. Pastikan ekstensi ini aktif di server.
- Jika latar belakang belum tersedia secara lokal, plugin akan mencoba mengunduhnya secara otomatis ke folder aset plugin.
