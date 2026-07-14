import re

with open('resources/views/manajer/observasi/create.blade.php', 'r') as f:
    content = f.read()

content = content.replace(
    '<span class="absolute -bottom-8 text-xs font-bold w-max text-center"',
    '<span class="absolute -bottom-8 left-1/2 -translate-x-1/2 text-xs font-bold w-max text-center"'
)

with open('resources/views/manajer/observasi/create.blade.php', 'w') as f:
    f.write(content)

print("Text centered.")
