with open('resources/views/manajer/observasi/create.blade.php', 'r') as f:
    content = f.read()

# I will replace:
#                 <!-- STEP 2 -->
#                 <div x-show="step === 2" x-cloak>
# with:
#                 </div> <!-- Close Step 1 correctly -->
#                 <!-- STEP 2 -->
#                 <div x-show="step === 2" x-cloak>

if "</div> <!-- Close Step 1 correctly -->" not in content:
    content = content.replace("                <!-- STEP 2 -->\n                <div x-show=\"step === 2\" x-cloak>", "                </div>\n                <!-- STEP 2 -->\n                <div x-show=\"step === 2\" x-cloak>")

# Also we have template x-if="step === 4" inside STEP 2 which needs to be step === 2
content = content.replace('<template x-if="step === 4">', '<template x-if="step === 2">')

with open('resources/views/manajer/observasi/create.blade.php', 'w') as f:
    f.write(content)

print("Fixes applied.")
