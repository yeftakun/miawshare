# MiawShare

Dikembangkan untuk memenuhi tugas mata kuliah pemrograman web.

<div align="center" style="flex: 1;">
        <img src="assets/logo/logo.png" alt="gambar" width=50%/>
</div>

## Aboutℹ️

MiawShare adalah platform komunitas online untuk berbagi karya, ide kreatif, dan gambar menarik. Dengan platform kami yang ramah pengguna, anda dapat dengan mudah menjelajahi berbagai ilustrasi oleh pengguna kami. Kami membangun MiawShare dengan tujuan untuk menciptakan komunitas yang inklusif dan mendukung, di mana setiap orang dapat merasa diterima dan dihargai. Kami menghargai keragaman dalam segala bentuknya dan berkomitmen untuk menciptakan lingkungan yang aman bagi semua orang untuk berekspresi dan berbagi.

## Languages & Database💻

![HTML5](https://img.shields.io/badge/html5-%23E34F26.svg?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/css3-%231572B6.svg?style=for-the-badge&logo=css3&logoColor=white)
![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/mysql-4479A1.svg?style=for-the-badge&logo=mysql&logoColor=white)

## Feature💡

- Pendaftaran pengguna dan verifikasi OTP
- Notifikasi via [Bot Telegram](https://t.me/spamtestingbot)
- Upload dan hapus postingan
- Pencarian postingan lewat judul, deskripsi dan pengupload
- Edit informasi pengguna dan hapus akun
- Regenerate OTP, hapus postingan, tambahkan/edit/hapus pengguna (Admin)

## Credit📜

- MiawShare Official Logo: [by: RyanManganguwi](https://www.instagram.com/enokki43at)
- Gambar tambahan: [Forbiden Page](https://tenor.com/j5llAKTW5xF.gif) | [Not Found](https://tenor.com/usTAOkJQpDE.gif) | [Bye-bye](https://tenor.com/uzAzw3pnkQ7.gif) | [Default Profile](http://opening.download/view.php?pic=https://i.pinimg.com/474x/94/cb/68/94cb68baea50bb98cdab65b74e731c1c.jpg)
- Referensi: [Pinterest](https://www.pinterest.com/) | [Pixiv](https://www.pixiv.net/en/)
- Hosting: [IdCloudHost](https://my.idcloudhost.com/clientarea.php)
- Our team: [Yefta Asyel](https://github.com/yeftakun/) (Backend) | [Andro Lay](https://github.com/AndroLay/) (Frontend) | [Ryan Manganguwi](https://github.com/RyanManganguwi/) (Design & Frontend) | [Aulia Nurhaliza](https://github.com/AuliaNurhaliza/) (Frontend)

## Pembuatan Akun

Akun user yang kami (admin) buat masih belum bisa menerima notifikasi karena kami belum menyertakan Chat Id telegram dari pengguna. Namun status dari akun tersebut sudah aktif dan siap digunakan.

**Cara pembuatan akun:**

- Isi form yang ada, pastikan username dan chat id belum terdaftar di database kami.
- Sebelum pendaftar klik "Buat", terlebih dahulu menuliskan pesan apapun di bot agar bot dapat mengenali chat id pengguna.
- Setelah itu pendaftar akun dapat klik "Buat" dan melihat kode OTP yang terkirim di telegram.
- Masukan username dan kode OTP di page verifikasi, lalu klik Verifikasi. Pengguna akan diarahkan ke page login untuk melakukan login (status akun pengguna telah "Aktif").
- PERHATIAN! Kode OTP dan Akun yang berstatus "Nonaktif" (sejak mendaftar) akan terhapus selama 3 menit oleh event scheduler di database. Jika sudah terhapus, pendaftar dapat melakukan pendaftaran ulang dengan username & chat id yang sama seperti sebelumnya (karena data user telah terhapus).
