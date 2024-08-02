<div class="vmb-widgets-container">
    <div class="container mt-5">
        <h2>Specials Management</h2>     
    
        <table class="table table-striped table-bordered mt-3" id="specialsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Resort</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Expiration</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Table rows will be inserted here -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for adding/editing special -->
<div class="modal fade" id="specialModal" tabindex="-1" aria-labelledby="specialModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="specialModalLabel">Add New Special</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="specialForm">
                    <div class="mb-3">
                        <label for="specialId" class="form-label">ID:</label>
                        <input type="text" readonly disabled class="form-control dt-input" id="specialId" required>
                    </div>
                    <div class="mb-3">
                        <label for="specialResort" class="form-label">Resort:</label>
                        <input type="text" readonly disabled class="form-control dt-input" id="specialResort" required>
                    </div>
                    <div class="mb-3">
                        <label for="specialName" class="form-label">Name:</label>
                        <input type="text" class="form-control dt-input" id="specialName" required>
                    </div>
                    <div class="mb-3">
                        <label for="specialDescription" class="form-label">Description:</label>
                        <textarea class="form-control dt-input" id="specialDescription" required></textarea>
                    </div>
                    <div class="mb-3" style="display:none;">
                        <label for="specialExpiration" class="form-label">Expiration:</label>
                        <input type="date" readonly disabled class="form-control dt-input" id="specialExpiration" required>
                    </div>
                    <div class="mb-3">
                        <label for="specialCategory" class="form-label">Category:</label>
                        <select class="form-control dt-input" id="specialCategory"></select>
                    </div>
  
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="specialDisable">
                        <label class="form-check-label" for="specialDisable">
                            Disable?
                        </label>
                    </div>
                    <input type="hidden" id="editSpecialIndex">
                    <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>

    // JavaScript to handle form submission and add/update row to the table for specials
    document.getElementById('specialForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent the form from submitting the traditional way

        // categories
        const categories = JSON.parse(vmb_ajax.cached_special_categories);

        // Get the values from the form fields
        const name = document.getElementById('specialName').value;
        const description = document.getElementById('specialDescription').value;
        const category = document.getElementById('specialCategory').value;
        const disable = document.getElementById('specialDisable').checked;
        const editSpecialIndex = document.getElementById('editSpecialIndex').value;

        const table = document.getElementById('specialsTable').getElementsByTagName('tbody')[0];

        if (editSpecialIndex === '') {
            // Add new special
            const newRow = table.insertRow();
            newRow.setAttribute('data-modified', 'false'); // Add modified attribute
            newRow.setAttribute('data-disable', disable); // Add disable attribute

            const nameCell = newRow.insertCell(2);
            const descriptionCell = newRow.insertCell(3);
            const categoryCell = newRow.insertCell(5);
            const actionCell = newRow.insertCell(6);

            nameCell.textContent = name;
            descriptionCell.textContent = description;
            categoryCell.innerHTML = category;
            actionCell.innerHTML = `
                <button class="btn btn-sm btn-warning" onclick="editSpecial(${table.rows.length - 1})">Edit</button>
                <button class="btn btn-sm ${disable ? 'btn-danger' : 'btn-secondary'}" onclick="toggleDisableSpecial(${table.rows.length - 1}, this)">
                    ${disable ? 'Disabled' : 'Disable'}
                </button>
            `;
        } else {
            // Update existing special
            const dataTable = jQuery('#specialsTable').DataTable();
            const row = dataTable.row(editSpecialIndex).node();
            row.cells[2].textContent = name;
            row.cells[3].textContent = description;
            row.cells[5].textContent = category;
            row.setAttribute('data-modified', 'true'); // Mark row as modified
            row.setAttribute('data-disable', disable); // Mark row as disabled


            const disableButton = row.cells[6].querySelector('button.btn-sm.btn-danger, button.btn-sm.btn-secondary'); 
            disableButton.className = `btn btn-sm ${disable ? 'btn-danger' : 'btn-secondary'}`; 
            disableButton.textContent = disable ? 'Disabled' : 'Disable'; 

            // Redraw the table to reflect the changes
            dataTable.draw();
        }

        // Update the cached specials in the server
        const specials = [];
        const dataTable = jQuery('#specialsTable').DataTable();
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

        // Clear the form fields
        document.getElementById('specialForm').reset();

        // Hide the modal
        var specialModalElement = document.getElementById('specialModal');
        var specialModal = bootstrap.Modal.getInstance(specialModalElement);
        specialModal.hide();
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Parse specials data from localized variable
        const specials = JSON.parse(vmb_ajax.cached_specials);

        // Fetch categories and generate table
        fetchCategories().done(function(response) {
            if (response.success) {
                const categories = response.data;
                generateSpecialsTable(specials, categories);

                const dataTable = jQuery('#specialsTable').DataTable({
                    "pageLength": 20,
                    "lengthChange": false, // Disable the default length change dropdown
                });

                // Create and populate the resort dropdown
                const resorts = [...new Set(specials.map(special => special.resort))];
                const resortFilter = document.createElement('select');
                resortFilter.classList.add('form-control', 'form-control-sm', 'dt-input');
                resortFilter.innerHTML = '<option value="">All Resorts</option>';

                resorts.forEach(resort => {
                    const option = document.createElement('option');
                    option.value = resort;
                    option.textContent = resort;
                    resortFilter.appendChild(option);
                });

                // Insert the resort filter where the length selector would be
                const lengthLabel = document.querySelector('#specialsTable_wrapper .dt-layout-start');
                lengthLabel.innerHTML = ''; // Clear the original content
                lengthLabel.appendChild(document.createTextNode('Filter by Resort: '));
                lengthLabel.appendChild(resortFilter);

                // Custom filtering function for resort
                jQuery.fn.dataTable.ext.search.push(
                    function(settings, data, dataIndex) {
                        const selectedResort = resortFilter.value;
                        const resort = data[1]; // Resort column data

                        if (selectedResort === "" || resort === selectedResort) {
                            return true;
                        }
                        return false;
                    }
                );

                // Filter table based on resort selection
                resortFilter.addEventListener('change', function() {
                    dataTable.draw();
                });
            } else {
                alert('Failed to load categories');
            }
        });
    });

</script>
