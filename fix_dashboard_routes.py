import re

with open('resources/views/dashboard.blade.php', 'r') as f:
    content = f.read()

# Remove the perhitungan quick link block
perhitungan_pattern = r"            @can\('process perhitungan'\)\n\s*<a href=\"\{\{ route\('manajer\.perhitungan\.index'\) \}\}.*?</a>\n\s*@endcan\n\s*"
content = re.sub(perhitungan_pattern, '', content, flags=re.DOTALL)

# Replace manajer.hasil.index with manajer.history.index in quick links
content = content.replace("route('manajer.hasil.index')", "route('manajer.history.index')")
content = content.replace("Lihat Semua Hasil", "Lihat Riwayat Penilaian")
content = content.replace("Hasil Keputusan", "Riwayat Penilaian")

with open('resources/views/dashboard.blade.php', 'w') as f:
    f.write(content)

print("Fixed dashboard routes.")
