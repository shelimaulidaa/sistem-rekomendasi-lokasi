import re

with open('resources/views/manajer/observasi/create.blade.php', 'r') as f:
    content = f.read()

content = content.replace('<input type="hidden" name="latitude" x-model="lat">', '<input type="hidden" name="latitude" :value="lat">')
content = content.replace('<input type="hidden" name="longitude" x-model="lng">', '<input type="hidden" name="longitude" :value="lng">')

with open('resources/views/manajer/observasi/create.blade.php', 'w') as f:
    f.write(content)

print("Updated hidden inputs to use :value")
