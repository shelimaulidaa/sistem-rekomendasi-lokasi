import re

with open('resources/views/manajer/observasi/create.blade.php', 'r') as f:
    content = f.read()

# We want to replace the x-if="step === 1" logic to NOT destroy the inputs.
# Let's add hidden inputs for latitude and longitude at the very end of the form, just to be safe.
# Find the </form> tag
form_end = content.find('</form>')
if form_end != -1:
    hidden_inputs = """
            <!-- Hidden inputs to ensure they are submitted regardless of step -->
            <input type="hidden" name="latitude" x-model="lat">
            <input type="hidden" name="longitude" x-model="lng">
"""
    # But wait, if they have the same name as the text inputs, the text inputs might override them with empty values if they are NOT removed?
    # If step === 2, text inputs are removed. So hidden inputs win.
    # If step === 1 (e.g. submit on step 1 if we had 1 step), text inputs exist. They have the same x-model, so they have the same value.
    # To be totally safe, we can just remove `name="latitude"` from the text inputs!
    
    content = content.replace('name="latitude"', '')
    content = content.replace('name="longitude"', '')
    
    # And add them as hidden inputs before </form>
    content = content[:form_end] + hidden_inputs + content[form_end:]
    
with open('resources/views/manajer/observasi/create.blade.php', 'w') as f:
    f.write(content)

print("Fixed latitude and longitude submission!")
