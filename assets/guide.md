# Panduan

Di bawah ini adalah panduan untuk berbagai fitur dan proses dalam sistem. Panduan ini akan diperbarui secara berkala sesuai kebutuhan.

---
<!--
### > Akun Sample
Berikut akun yang sudah tersedia di database:

| Username | Password | Level     |
|----------|----------|-----------|
| admin    | 123      | 1 (Admin) |
| yefta    | 123      | 2 (User)  |
| bocchi   | 123      | 2 (User)  |

-->

### > Pembuatan Akun
Akun user yang kami (admin) buat masih belum bisa menerima notifikasi karena kami belum menyertakan Chat Id telegram dari pengguna. Namun status dari akun tersebut sudah aktif dan siap digunakan.

**Cara pembuatan akun:**

- Isi form yang ada, pastikan username dan chat id belum terdaftar di database kami.
- Sebelum pendaftar klik "Buat", terlebih dahulu menuliskan pesan apapun di bot agar bot dapat mengenali chat id pengguna.
- Setelah itu pendaftar akun dapat klik "Buat" dan melihat kode OTP yang terkirim di telegram.
- Masukan username dan kode OTP di page verifikasi, lalu klik Verifikasi. Pengguna akan diarahkan ke page login untuk melakukan login (status akun pengguna telah "Aktif").
- PERHATIAN! Kode OTP dan Akun yang berstatus "Nonaktif" (sejak mendaftar) akan terhapus selama 3 menit oleh event scheduler di database. Jika sudah terhapus, pendaftar dapat melakukan pendaftaran ulang dengan username & chat id yang sama seperti sebelumnya (karena data user telah terhapus).
