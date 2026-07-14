import re

with open('resources/views/manajer/observasi/show.blade.php', 'r') as f:
    content = f.read()

content = content.replace(
    '<p class="text-sm font-bold text-base-dark">{{ $observasi->luas_bangunan }} m²</p>',
    '<p class="text-sm font-bold text-base-dark">{{ floatval($observasi->luas_bangunan) }} m²</p>'
)

content = content.replace(
    '<p class="text-sm font-bold text-base-dark">{{ $observasi->luas_tanah }} m²</p>',
    '<p class="text-sm font-bold text-base-dark">{{ floatval($observasi->luas_tanah) }} m²</p>'
)

with open('resources/views/manajer/observasi/show.blade.php', 'w') as f:
    f.write(content)

print("Decimals formatted.")
