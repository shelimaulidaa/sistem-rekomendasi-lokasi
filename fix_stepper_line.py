import re

with open('resources/views/manajer/observasi/create.blade.php', 'r') as f:
    content = f.read()

content = content.replace(
    '<div class="absolute top-1/2 left-0 w-full -translate-y-1/2" aria-hidden="true">',
    '<div class="absolute top-1/2 left-5 right-5 -translate-y-1/2" aria-hidden="true">'
)

with open('resources/views/manajer/observasi/create.blade.php', 'w') as f:
    f.write(content)

print("Line adjusted.")
