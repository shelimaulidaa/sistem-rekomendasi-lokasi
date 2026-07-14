import re

with open('resources/views/manajer/observasi/create.blade.php', 'r') as f:
    content = f.read()

setup_marker = """
                setupMarkerEvents() {
                    this.marker.on('dragend', (e) => {
                        const coords = e.target.getLatLng();
                        this.lat = coords.lat.toFixed(6);
                        this.lng = coords.lng.toFixed(6);
                        this.debouncedGeocode(coords.lat, coords.lng);
                        
                        // Trigger spatial data fetch on drag end
                        window.dispatchEvent(new CustomEvent('location-updated', { detail: { lat: coords.lat, lng: coords.lng } }));
                    });
                },"""

content = re.sub(r"setupMarkerEvents\(\) \{.*?(?=\s+getCurrentLocation\(\) \{)", setup_marker, content, flags=re.DOTALL)

with open('resources/views/manajer/observasi/create.blade.php', 'w') as f:
    f.write(content)

print("Fixed setupMarkerEvents.")
