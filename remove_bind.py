import re

with open('resources/views/manajer/observasi/create.blade.php', 'r') as f:
    content = f.read()

content = content.replace('<input type="hidden" name="latitude" :value="lat">', '<input type="hidden" name="latitude" id="hidden_lat">')
content = content.replace('<input type="hidden" name="longitude" :value="lng">', '<input type="hidden" name="longitude" id="hidden_lng">')

with open('resources/views/manajer/observasi/create.blade.php', 'w') as f:
    f.write(content)

print("Removed Alpine bind from hidden inputs")
