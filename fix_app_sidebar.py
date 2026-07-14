import re

with open('resources/views/layouts/app.blade.php', 'r') as f:
    content = f.read()

# Remove perhitungan block
pattern = r"                @can\('process perhitungan'\)\n\s*<x-sidebar-link :href=\"route\('manajer\.perhitungan\.index'\)\".*?</x-sidebar-link>\n\s*@endcan\n\n"
content = re.sub(pattern, '', content, flags=re.DOTALL)

# Replace hasil block with history
content = content.replace("route('manajer.hasil.index')", "route('manajer.history.index')")
content = content.replace("request()->routeIs('manajer.hasil.*')", "request()->routeIs('manajer.history.*')")
content = content.replace("Hasil Keputusan", "Riwayat Penilaian")

with open('resources/views/layouts/app.blade.php', 'w') as f:
    f.write(content)

print("Fixed app sidebar.")
