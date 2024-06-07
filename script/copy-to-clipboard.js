document.getElementById("copyButton").addEventListener("click", function() {
    // Mendapatkan URL halaman
    var url = window.location.href;

    // Membuat elemen textarea yang tidak terlihat
    var textarea = document.createElement("textarea");
    textarea.value = url;
    document.body.appendChild(textarea);

    // Memilih teks dalam textarea
    textarea.select();
    textarea.setSelectionRange(0, 99999); // Untuk browser yang tidak support

    // Menyalin teks yang dipilih ke clipboard
    document.execCommand("copy");

    // Menghapus elemen textarea yang dibuat
    document.body.removeChild(textarea);

    // Memberi tahu pengguna bahwa URL telah disalin
    alert("URL telah disalin ke clipboard");
});