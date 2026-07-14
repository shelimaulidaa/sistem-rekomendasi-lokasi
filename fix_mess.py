import re

with open('resources/views/manajer/observasi/create.blade.php', 'r') as f:
    content = f.read()

# Remove the broken part
broken_part = """    <!-- Al
            <!-- Hidden inputs to ensure they are submitted regardless of step -->
            <input type="hidden" name="latitude" id="hidden_lat">
            <input type="hidden" name="longitude" id="hidden_lng">
pine Image Uploader Logic -->"""
content = content.replace(broken_part, "    <!-- Alpine Image Uploader Logic -->")

# Now properly insert before </form>
content = content.replace('</form>', """            <!-- Hidden inputs to ensure they are submitted regardless of step -->
            <input type="hidden" name="latitude" id="hidden_lat">
            <input type="hidden" name="longitude" id="hidden_lng">
        </form>""")

with open('resources/views/manajer/observasi/create.blade.php', 'w') as f:
    f.write(content)

print("Fixed the mess")
