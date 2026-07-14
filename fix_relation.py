import re

with open('app/Http/Controllers/Manajer/PerhitunganController.php', 'r') as f:
    content = f.read()

content = content.replace("HasilPerhitungan::with('penilaian.lokasi')", "HasilPerhitungan::with('penilaian.observasiLokasi')")

with open('app/Http/Controllers/Manajer/PerhitunganController.php', 'w') as f:
    f.write(content)

print("Fixed Relation Error!")
