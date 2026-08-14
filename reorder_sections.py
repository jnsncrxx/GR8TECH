import re

with open('c:\\xampp\\htdocs\\GR8TECH\\resources\\views\\employees\\edit.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Define section markers
sections = {
    'personal': (re.search(r'\{\{-- ═══════════════════════════════════════════════\s+1\. PERSONAL INFORMATION.*?(?=\{\{-- ═══════════════════════════════════════════════)', content, re.DOTALL), 'sec-personal'),
    'id': (re.search(r'\{\{-- ═══════════════════════════════════════════════\s+IDENTIFICATION.*?(?=\{\{-- ═══════════════════════════════════════════════)', content, re.DOTALL), 'sec-id'),
    'work': (re.search(r'\{\{-- ═══════════════════════════════════════════════\s+2\. WORK INFORMATION.*?(?=\{\{-- ═══════════════════════════════════════════════)', content, re.DOTALL), 'sec-work'),
    'details': (re.search(r'\{\{-- ═══════════════════════════════════════════════\s+3\. ADDITIONAL EMPLOYEE DETAILS.*?(?=\{\{-- ═══════════════════════════════════════════════)', content, re.DOTALL), 'sec-details'),
    'emergency': (re.search(r'\{\{-- ═══════════════════════════════════════════════\s+4\. IN CASE OF EMERGENCY.*?(?=\{\{-- ═══════════════════════════════════════════════)', content, re.DOTALL), 'sec-emergency'),
    'loans': (re.search(r'\{\{-- ═══════════════════════════════════════════════\s+5\. EMPLOYEE LOANS.*?(?=\{\{-- ═══════════════════════════════════════════════)', content, re.DOTALL), 'sec-loans'),
    'banking': (re.search(r'\{\{-- ═══════════════════════════════════════════════\s+6\. BANKING & IDS.*?(?=        <div style="display:flex;justify-content:flex-end;gap:1rem;)', content, re.DOTALL), 'sec-banking'),
}

# Ensure all found
for k, v in sections.items():
    if not v[0]:
        print(f'Missing: {k}')
        exit(1)

# Extract text
blocks = {k: v[0].group(0) for k, v in sections.items()}

# Construct the new layout
left_col = f'<div style="display:flex;flex-direction:column;gap:1.5rem;">\n{blocks["id"]}{blocks["personal"]}{blocks["banking"]}{blocks["loans"]}</div>'
right_col = f'<div style="display:flex;flex-direction:column;gap:1.5rem;">\n{blocks["work"]}{blocks["details"]}{blocks["emergency"]}</div>'

grid = f'<div class="grid-2" style="align-items:start;">\n{left_col}\n{right_col}\n</div>\n\n'

# Find start and end indices for replacement
start_idx = sections['personal'][0].start()
end_idx = sections['banking'][0].end()

new_content = content[:start_idx] + grid + content[end_idx:]

with open('c:\\xampp\\htdocs\\GR8TECH\\resources\\views\\employees\\edit.blade.php', 'w', encoding='utf-8') as f:
    f.write(new_content)
print('Done!')
