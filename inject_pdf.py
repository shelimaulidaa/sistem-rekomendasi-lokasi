import re

with open('resources/views/manajer/history/pdf.blade.php', 'r') as f:
    content = f.read()

# Replace title
content = content.replace("Hasil Keputusan TOPSIS", "Riwayat Penilaian TOPSIS")
content = content.replace("Laporan eksekutif hasil akhir rekomendasi pemilihan lokasi cabang.", "Laporan eksekutif riwayat hasil rekomendasi pemilihan lokasi cabang. <br> <strong>Batch:</strong> {{ $batchName }}")

with open('resources/views/manajer/history/pdf.blade.php', 'w') as f:
    f.write(content)

print("Updated pdf.")
