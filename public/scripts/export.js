document.getElementById('exportBtn').addEventListener('click', function() {
    const exportContent = document.getElementById('exportContent').cloneNode(true);
    const buttons = exportContent.querySelectorAll('button');
    buttons.forEach(button => button.remove());

    const tempDiv = document.createElement('div');
    tempDiv.style.textAlign = 'center';
    tempDiv.appendChild(exportContent);
    document.body.appendChild(tempDiv);

    const wb = XLSX.utils.table_to_book(exportContent.querySelector('table'), {
        sheet: "Inventory",
        raw: true
    });

    const today = new Date();
    const dateString = today.getFullYear() + '-' + 
                      (today.getMonth() + 1).toString().padStart(2, '0') + '-' + 
                      today.getDate().toString().padStart(2, '0');
    const filename = `OCD_Inventory_Report_${dateString}.xlsx`;

    XLSX.writeFile(wb, filename);
    document.body.removeChild(tempDiv);
});

document.getElementById('exportPdfBtn').addEventListener('click', function () {
    const element = document.getElementById('exportContent');
    element.style.width = element.scrollWidth + 'px';
    element.style.transform = 'scale(0.84) translateX(-90px)';
    element.style.transformOrigin = 'center top';

    const opt = {
        margin: 0,
        filename: 'OCD_Inventory_Report.pdf',
        image: { type: 'jpeg', quality: 1 },
        html2canvas: {
            scale: 1,
            scrollX: 0,
            scrollY: -window.scrollY,
            windowWidth: element.scrollWidth,
            useCORS: true
        },
        jsPDF: {
            unit: 'mm',
            format: 'a4',
            orientation: 'landscape'
        }
    };

    html2pdf().set(opt).from(element).save().then(() => {
        element.style.width = '';
        element.style.transform = '';
    });
});

document.getElementById('printBtn').addEventListener('click', function() {
    window.print();
});

document.getElementById('exportWordBtn').addEventListener('click', function() {
    const content = document.getElementById('exportContent').cloneNode(true);
    const buttons = content.querySelectorAll('button');
    buttons.forEach(button => button.remove());

    const wrapper = document.createElement('div');
    wrapper.style.textAlign = 'center';
    wrapper.appendChild(content);

    const converted = htmlDocx.asBlob(wrapper.innerHTML);
    saveAs(converted, 'OCD_Inventory_Report.docx');
});
