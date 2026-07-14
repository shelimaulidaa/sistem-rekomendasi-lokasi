import re

with open('resources/views/manajer/observasi/create.blade.php', 'r') as f:
    content = f.read()

# 1. Dispatch event in updateMarker
update_marker = """
                updateMarker(lat, lng, doGeocode = false) {
                    this.lat = lat.toFixed(6);
                    this.lng = lng.toFixed(6);
                    if (this.marker) {
                        this.marker.setLatLng([lat, lng]);
                    } else {
                        this.marker = L.marker([lat, lng], { draggable: true, icon: this.greenIcon }).addTo(this.map);
                        this.setupMarkerEvents();
                    }
                    if (doGeocode) {
                        this.debouncedGeocode(lat, lng);
                    }
                    
                    // Trigger spatial data fetch
                    window.dispatchEvent(new CustomEvent('location-updated', { detail: { lat: lat, lng: lng } }));
                },"""

content = re.sub(r"updateMarker\(lat, lng, doGeocode = false\) \{.*?(?=\s+setupMarkerEvents\(\) \{)", update_marker, content, flags=re.DOTALL)

# 2. Fix the nested x-data having the @location-updated.window attribute
# We look for the Section 2 Data Utama div
section2_match = re.search(r'(<!-- Section 2: Data Utama -->.*?<div class="[^"]*?")\s*@location-updated\.window="fetchSpatialData\(\$event\.detail\.lat, \$event\.detail\.lng\)"', content, flags=re.DOTALL)

if section2_match:
    content = content.replace(section2_match.group(0), section2_match.group(1))

# Let's also check if there are other errant instances of @location-updated.window
# It should ONLY be on the main <form> tag.
form_tag = """<form method="POST" action="{{ route('manajer.observasi.store') }}" enctype="multipart/form-data"
              @location-updated.window="fetchSpatialData($event.detail.lat, $event.detail.lng)"
              x-data="{"""
# if it's there, great. We will just remove any other ones.
count = content.count('@location-updated.window="fetchSpatialData($event.detail.lat, $event.detail.lng)"')
print("Count of @location-updated.window before fix:", count)

# I will just replace all, then add it exactly to the form tag.
content = content.replace('@location-updated.window="fetchSpatialData($event.detail.lat, $event.detail.lng)"', "")
content = content.replace('enctype="multipart/form-data"', 'enctype="multipart/form-data"\n              @location-updated.window="fetchSpatialData($event.detail.lat, $event.detail.lng)"')

with open('resources/views/manajer/observasi/create.blade.php', 'w') as f:
    f.write(content)

print("Fixed event dispatching and listeners.")
