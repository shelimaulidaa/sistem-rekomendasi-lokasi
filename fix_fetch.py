import re

with open('resources/views/manajer/observasi/create.blade.php', 'r') as f:
    content = f.read()

# remove the getElementById line
content = content.replace("document.getElementById('jumlah_kompetitor').value = data.competitor_count;", "console.log('Spatial Data Fetched:', data);")

with open('resources/views/manajer/observasi/create.blade.php', 'w') as f:
    f.write(content)

print("Removed potentially faulty getElementById")
