import re

with open('resources/views/manajer/observasi/create.blade.php', 'r') as f:
    content = f.read()

init_block = """
                  init() {
                      // Fetch automatically if coordinates exist
                      if (this.lat && this.lng) {
                          this.fetchSpatialData(this.lat, this.lng);
                      }
                      
                      // Also listen for location-updated directly in Alpine
                      window.addEventListener('location-updated', (e) => {
                          console.log('Location updated event received!', e.detail);
                          this.fetchSpatialData(e.detail.lat, e.detail.lng);
                      });
                  },
"""

# inject init() after get progress()
content = content.replace("get progress() {", init_block + "\n                  get progress() {")

with open('resources/views/manajer/observasi/create.blade.php', 'w') as f:
    f.write(content)

print("Added init() block for debugging and auto-fetching")
