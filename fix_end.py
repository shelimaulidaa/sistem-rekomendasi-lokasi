import re

with open('resources/views/manajer/observasi/create.blade.php', 'r') as f:
    content = f.read()

# Make sure there is a closing div for line 8 before @push('scripts')
if "    </div>\n    @push('scripts')" not in content and "</form>\n    @push('scripts')" in content:
    content = content.replace("</form>\n    @push('scripts')", "</form>\n    </div>\n    @push('scripts')")

with open('resources/views/manajer/observasi/create.blade.php', 'w') as f:
    f.write(content)

print("Done")
