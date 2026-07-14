import re

with open('resources/views/manajer/observasi/create.blade.php', 'r') as f:
    content = f.read()

# Fix the missing </div>
content = content.replace("<!-- Step 1 Nav -->\n<!-- Step 1 Nav -->", "</div>\n                        <!-- Step 1 Nav -->")

# Also, there's another replace:
if "</div>\n                        <!-- Step 1 Nav -->" not in content and "<!-- Step 1 Nav -->" in content:
    # Just to be sure we have the closing div
    content = re.sub(r'(\s+)<!-- Step 1 Nav -->', r'\1</div>\1<!-- Step 1 Nav -->', content, count=1)

# Fix the RPH Modal (Remove AKSI column and Gunakan Ini button)
rph_th = """                                            <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">JARAK (KM)</th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">AKSI</th>"""
new_rph_th = """                                            <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">JARAK (KM)</th>"""
content = content.replace(rph_th, new_rph_th)

rph_td = """                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center space-x-2">
                                                    <button type="button" @click="rphName = item.nama; jarakRphDisplay = item.distance; document.getElementById('jarak_rph_display').value = item.distance;" class="text-green-600 hover:text-green-900 bg-green-50 px-3 py-1 rounded border border-green-200">Gunakan Ini</button>
                                                </td>"""
content = content.replace(rph_td, "")

# adjust colspan from 4 to 3
content = content.replace('<td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada RPH di sekitar.</td>', '<td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada RPH di sekitar.</td>')

with open('resources/views/manajer/observasi/create.blade.php', 'w') as f:
    f.write(content)

print("Fixed HTML issues.")
