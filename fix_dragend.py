import re

with open('resources/views/manajer/observasi/create.blade.php', 'r') as f:
    content = f.read()

replacement = """                    this.marker.on('dragend', (e) => {
                        const coords = e.target.getLatLng();
                        this.lat = coords.lat.toFixed(6);
                        this.lng = coords.lng.toFixed(6);
                        
                        let latInput = document.querySelector('input[name="latitude"]');
                        let lngInput = document.querySelector('input[name="longitude"]');
                        if(latInput) latInput.value = this.lat;
                        if(lngInput) lngInput.value = this.lng;"""

content = content.replace("""                    this.marker.on('dragend', (e) => {
                        const coords = e.target.getLatLng();
                        this.lat = coords.lat.toFixed(6);
                        this.lng = coords.lng.toFixed(6);""", replacement)

with open('resources/views/manajer/observasi/create.blade.php', 'w') as f:
    f.write(content)

print("Updated dragend")
