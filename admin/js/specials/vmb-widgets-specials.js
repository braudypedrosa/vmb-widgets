// Generate table rows with dropdowns for categories
function generateSpecialsTable(specials, categories) {
    const tableBody = document.getElementById('specialsTable').getElementsByTagName('tbody')[0];
    tableBody.innerHTML = '';

    specials.forEach((special, index) => {
        const newRow = tableBody.insertRow();
        newRow.setAttribute('data-modified', special.modified); // Add modified attribute
        newRow.setAttribute('data-disable', special.disable); // new update: Add disable attribute
        newRow.innerHTML = `
            <td>${special.id}</td>
            <td>${special.resort}</td>
            <td>${special.name}</td>
            <td>${special.description}</td>
            <td>${formatDate(special.expiration)}</td>
            <td>${special.category ? special.category : ''}</td>
            <td class="action-buttons">
                <button class="btn btn-sm btn-warning" onclick="editSpecial(${index})">Edit</button>
                <button class="btn btn-sm ${special.disable ? 'btn-danger' : 'btn-secondary'}" onclick="toggleDisableSpecial(${index}, this)">
                    ${special.disable ? 'Disabled' : 'Disable'}
                </button>
            </td>
        `; // new update: Add Disable button with dynamic classes and text
    });
}


// Function to reset the form and modal title for specials
function resetSpecialForm() {
    document.getElementById('specialForm').reset();
    document.getElementById('specialModalLabel').textContent = 'Add New Special';
    document.getElementById('editSpecialIndex').value = '';
}

// Function to save the specials to the server
function saveSpecials(specials) {
    jQuery.ajax({
        url: vmb_ajax.ajax_url,
        type: 'POST',
        data: {
            action: 'save_table',
            jsonData: JSON.stringify(specials),
        },
        success: function(response) {
            if (!response.success) {
                alert('Failed to save specials');
            } else {
                Swal.fire(
                    'Saved!',
                    '',
                    'success'
                );
            }
        }
    });
}

// Function to edit a special
function editSpecial(visualIndex) {
    const dataTable = jQuery('#specialsTable').DataTable();
    const row = dataTable.row(visualIndex).node();
    const cells = row.getElementsByTagName('td');

    const id = cells[0].textContent;
    const resort = cells[1].textContent;
    const name = cells[2].textContent;
    const description = cells[3].textContent;
    const expiration = cells[4].textContent;
    const category = cells[5].textContent;

    document.getElementById('specialId').value = id;
    document.getElementById('specialResort').value = resort;
    document.getElementById('specialName').value = name;
    document.getElementById('specialDescription').value = description;
    document.getElementById('specialExpiration').value = expiration;
    document.getElementById('specialDisable').checked = row.getAttribute('data-disable') === 'true';

    // Populate the category dropdown with options
    fetchCategories().done(function(response) {
        if (response.success) {
            const categories = response.data;
            const categorySelect = document.getElementById('specialCategory');
            categorySelect.innerHTML = categories.map(cat => `
                <option value="${cat.name}" ${cat.name === category ? 'selected' : ''}>
                    ${cat.name}
                </option>
            `).join('');
        } else {
            alert('Failed to load categories');
        }
    });

    document.getElementById('editSpecialIndex').value = visualIndex;
    document.getElementById('specialModalLabel').textContent = 'Edit Special';

    var specialModal = new bootstrap.Modal(document.getElementById('specialModal'));
    specialModal.show();
}

function toggleDisableSpecial(index, button) { 
    const dataTable = jQuery('#specialsTable').DataTable();
    const row = dataTable.row(index).node();
    const isDisabled = row.getAttribute('data-disable') === 'true';

    row.setAttribute('data-disable', !isDisabled);
    button.className = `btn btn-sm ${!isDisabled ? 'btn-danger' : 'btn-secondary'}`;
    button.textContent = !isDisabled ? 'Disabled' : 'Disable';
    
    // Update the cached specials in the server
    const specials = [];
    const rows = dataTable.rows().nodes(); // Get all rows as an array of nodes

    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        specials.push({
            id: row.cells[0].textContent,
            resort: row.cells[1].textContent,
            name: row.cells[2].textContent,
            description: row.cells[3].textContent,
            expiration: row.cells[4].textContent,
            category: row.cells[5].textContent,
            disable: row.getAttribute('data-disable') === 'true', // Include disable attribute
            modified: row.getAttribute('data-modified') === 'true' // Include modified attribute
        });
    }

    saveSpecials(specials);
}