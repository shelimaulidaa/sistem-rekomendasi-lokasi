import re

with open('resources/views/manajer/observasi/create.blade.php', 'r') as f:
    content = f.read()

replacement = """                updateMarker(lat, lng, doGeocode = false) {
                    this.lat = lat.toFixed(6);
                    this.lng = lng.toFixed(6);
                    
                    // Directly update the hidden inputs to guarantee form submission
                    let latInput = document.querySelector('input[name="latitude"]');
                    let lngInput = document.querySelector('input[name="longitude"]');
                    if(latInput) latInput.value = this.lat;
                    if(lngInput) lngInput.value = this.lng;"""

content = content.replace("                updateMarker(lat, lng, doGeocode = false) {\n                    this.lat = lat.toFixed(6);\n                    this.lng = lng.toFixed(6);", replacement)

with open('resources/views/manajer/observasi/create.blade.php', 'w') as f:
    f.write(content)

print("Updated updateMarker")
