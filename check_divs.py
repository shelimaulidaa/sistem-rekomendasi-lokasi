import re

with open('step1.html', 'r') as f:
    html = f.read()

# remove comments
html = re.sub(r'<!--.*?-->', '', html, flags=re.DOTALL)

open_divs = len(re.findall(r'<div\b', html, re.IGNORECASE))
close_divs = len(re.findall(r'</div\b', html, re.IGNORECASE))

print(f"Open: {open_divs}, Close: {close_divs}")
