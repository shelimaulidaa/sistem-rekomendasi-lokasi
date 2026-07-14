import re

with open('resources/views/manajer/observasi/create.blade.php', 'r') as f:
    content = f.read()

hidden_inputs = """                                    <!-- Hidden Inputs to submit names -->
                                    <input type="hidden" name="provinsi" :value="provName">
                                    <input type="hidden" name="kabupaten_kota" :value="regName">
                                    <input type="hidden" name="kecamatan" :value="distName">
                                    <input type="hidden" name="umk" :value="umk_kota">
                                    <input type="hidden" name="pdrb" :value="pdrb_kota">
                                    <input type="hidden" name="jumlah_penduduk_muslim" :value="penduduk_muslim_kota">"""

content = content.replace("                                    <!-- Hidden Inputs to submit names -->\n                                    <input type=\"hidden\" name=\"provinsi\" :value=\"provName\">\n                                    <input type=\"hidden\" name=\"kabupaten_kota\" :value=\"regName\">\n                                    <input type=\"hidden\" name=\"kecamatan\" :value=\"distName\">", hidden_inputs)

with open('resources/views/manajer/observasi/create.blade.php', 'w') as f:
    f.write(content)

print("Updated create.blade.php hidden inputs.")
