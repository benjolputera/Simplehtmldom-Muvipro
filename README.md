# Simplehtmldom-Muvipro
Implementasi PHP Simple HTML DOM Parser untuk mengikis konten dari website yang menggunakan tema muvipro.

How to use : 
1. Download source code php, pindahkan ke folder htdocs. 
2. Atur nama database di file api.php
3. Kunjungi http://localhost/folderphpsourcecode/api.php

* Menu Let's start => melanjutkan ke menu lainnya
* Menu Scrapper URL => Mengikis data slug dari http://websitemovie.com/post-sitemap1.xml. Tidak akan bekerja jika website tidak mengunakan plugin Yoast.
* Menu Data URL => Menampilkan data Url dalam table
* Menu Auto Get Content => Otomatis mengikis data berdasarkan data url yang sudah di Scrapper.

extension crome for php => Sama seperti Auto Get Content, hanya saja menggunakan extension untuk mengambil data body dari website.
Cara menggunakan :
1. Navigasi ke chrome://extensions
2. Buka menu tarik-turun Pengembang dan klik “Muat Ekstensi yang Belum Dibongkar”
3. Arahkan ke folder lokal yang berisi kode ekstensi dan klik Ok
4. Kunjingi website yang telah diatur pada manifest.json, auto Scrapper bekerja 30 detik sekali. 
5. Reload page jika ada error

Sesuaikan url pada file manifest.json pada extension.
Dan sesuaikan juga url pada content.js urlDrakorID dan urlAPI.

Api :
* Kunjungi => http://localhost/folderphpsourcecode/api.php?main=api&page=1

Maaf untuk penulisan kode yang berantakan.
Jika ingin plugin simple post dari data api silakan hubungi ane. 
Email : benjolputera{@}gmail.com
