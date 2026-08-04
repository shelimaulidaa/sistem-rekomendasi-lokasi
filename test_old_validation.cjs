const puppeteer = require('puppeteer');

(async () => {
    console.log('=========================================================');
    console.log('    TESTING VALIDATION FAIL & OLD() RESTORATION FLOW');
    console.log('=========================================================\n');

    const browser = await puppeteer.launch({
        executablePath: 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
        headless: true,
        args: ['--no-sandbox', '--disable-setuid-sandbox']
    });
    
    const page = await browser.newPage();

    // Login
    await page.goto('http://127.0.0.1:8000/login', { waitUntil: 'networkidle2' });
    await page.type('input[name="username"]', 'manajer');
    await page.type('input[name="password"]', 'manajer123');
    await Promise.all([
        page.click('button[type="submit"]'),
        page.waitForNavigation({ waitUntil: 'networkidle2' })
    ]);

    // Go to Create Observasi Page
    await page.goto('http://127.0.0.1:8000/manajer/observasi/create', { waitUntil: 'networkidle2' });
    await new Promise(r => setTimeout(r, 2000));

    console.log('1. Simulating Step 1 selection (Provinsi: Jawa Barat, Kab: Bandung, Kec: Sumur Bandung)...');
    
    await page.evaluate(() => {
        window.dispatchEvent(new CustomEvent('address-resolved', {
            detail: {
                fullAddress: "Jl. Sunda No. 5, Kebon Pisang, Sumur Bandung, Kota Bandung, Jawa Barat 40112",
                state: "Jawa Barat",
                city: "Kota Bandung",
                district: "Sumur Bandung",
                suburb: "Kebon Pisang"
            }
        }));
    });

    await new Promise(r => setTimeout(r, 2500));

    // Submit form with missing step 2 fields via form element
    console.log('2. Submitting form with missing step 2 fields to trigger validation error...');
    
    await page.evaluate(() => {
        // Remove HTML5 required attribute
        document.querySelectorAll('[required]').forEach(el => el.removeAttribute('required'));
        // Fill only partial fields so validation fails on missing fields
        const form = document.querySelector('form');
        form.submit();
    });

    await page.waitForNavigation({ waitUntil: 'networkidle2' });
    console.log('Redirected URL after validation fail:', page.url());

    console.log('3. Waiting 3 seconds for Alpine & loadProvinces()...');
    await new Promise(r => setTimeout(r, 3000));

    console.log('4. Inspecting DOM dropdown states after old() restoration...');
    const result = await page.evaluate(() => {
        const provSelect = document.querySelector('select[name="province_id"]');
        const regSelect = document.querySelector('select[name="regency_id"]');
        const distSelect = document.querySelector('select[name="district_id"]');

        const container = provSelect ? provSelect.closest('[x-data]') : null;
        let alpine = null;
        if (container && window.Alpine) {
            alpine = window.Alpine.$data(container);
        }

        return {
            domProvValue: provSelect ? provSelect.value : null,
            domProvText: provSelect && provSelect.selectedOptions[0] ? provSelect.selectedOptions[0].text : null,
            domRegValue: regSelect ? regSelect.value : null,
            domRegText: regSelect && regSelect.selectedOptions[0] ? regSelect.selectedOptions[0].text : null,
            domDistValue: distSelect ? distSelect.value : null,
            domDistText: distSelect && distSelect.selectedOptions[0] ? distSelect.selectedOptions[0].text : null,
            alpineData: alpine ? {
                selectedProvId: alpine.selectedProvId,
                provName: alpine.provName,
                selectedRegId: alpine.selectedRegId,
                regName: alpine.regName,
                selectedDistId: alpine.selectedDistId,
                distName: alpine.distName
            } : null
        };
    });

    console.log('\n=========================================================');
    console.log('           OLD() RESTORATION TEST RESULTS              ');
    console.log('=========================================================');
    console.log(JSON.stringify(result, null, 2));

    await browser.close();
})();
