document.addEventListener('DOMContentLoaded', function() {
    var isDataSaved = true;
    var currentPage = 1;
    var rowsPerPage = 20;
    var specials = [];
    var allSpecials = []; // To store all results for filtering

    var categories = [
        "Beach Colony Resort",
        "Beach Cove Resort",
        "Captain’s Quarters Resort",
        "Carolina Winds Resort",
        "Crown Reef Resort and Waterpark",
        "Forest Dunes Resort",
        "hotel Blue",
        "Landmark Resort",
        "Ocean Creek Resort",
        "Ocean Escape Condos",
        "Palace Resort",
        "Palms Resort",
        "Sea Watch Resort",
        "South Wind On The Ocean",
        "The Caravelle Resort"
    ];

    function addRowToTable(specialId, resortId, resort, specialName, specialDescription, expiration, isDisabled) {
        var table = document.getElementById('specialsTable').getElementsByTagName('tbody')[0];
        var newRow = table.insertRow();
        var cell0 = newRow.insertCell(0); // Hidden cell for resortId
        var cell1 = newRow.insertCell(1);
        var cell2 = newRow.insertCell(2);
        var cell3 = newRow.insertCell(3);
        var cell4 = newRow.insertCell(4);
        var cell5 = newRow.insertCell(5);
        var cell6 = newRow.insertCell(6);

        cell0.innerHTML = resortId;
        cell0.style.display = 'none'; // Hide this cell
        cell1.innerHTML = specialId;
        cell2.innerHTML = resort;
        cell3.innerHTML = specialName;
        cell4.innerHTML = specialDescription;
        cell5.innerHTML = formatDate(expiration);
        cell6.innerHTML = '<button class="edit-btn">Edit</button> <button class="disable-btn">Disable</button>';

        cell1.classList.add('readonly');
        cell2.classList.add('readonly');
        cell5.classList.add('readonly');

        newRow.setAttribute('data-disabled', isDisabled);
        if (isDisabled) {
            newRow.classList.add('disabled');
        }

        makeEditable(newRow, false);
    }

    function makeEditable(row, editable) {
        var cells = row.getElementsByTagName('td');
        for (var i = 0; i < cells.length - 1; i++) {
            if (i === 0 || i === 1 || i === 2 || i === 5) {
                // Make Special ID, Resort, and Expiration columns readonly
                cells[i].setAttribute('contenteditable', 'false');
            } else {
                if (editable && !row.classList.contains('disabled')) {
                    cells[i].setAttribute('contenteditable', 'true');
                    cells[i].classList.add('editable');
                } else {
                    cells[i].setAttribute('contenteditable', 'false');
                    cells[i].classList.remove('editable');
                }
            }
        }
        var editButton = row.querySelector(".edit-btn");
        if (editable) {
            row.classList.add('edit-mode');
            editButton.textContent = "Save";
        } else {
            row.classList.remove('edit-mode');
            editButton.textContent = "Edit";
        }
    }

    function toggleDisable(row) {
        var isDisabled = row.getAttribute('data-disabled') === 'true';
        row.setAttribute('data-disabled', !isDisabled);
        if (isDisabled) {
            row.classList.remove('disabled');
        } else {
            row.classList.add('disabled');
        }
        makeEditable(row, !isDisabled);
        isDataSaved = false;
    }

    function toggleEdit(row) {
        var editable = row.classList.contains('edit-mode');
        if (editable) {
            // Save data
            makeEditable(row, false);
        } else {
            // Enable edit mode
            makeEditable(row, true);
        }
    }

    function loadJsonData(jsonData) {
        specials = JSON.parse(jsonData);
        allSpecials = JSON.parse(jsonData); // Keep all data for filtering
        displayTable();
        setupPagination();
    }

    function displayTable() {
        var table = document.getElementById('specialsTable').getElementsByTagName('tbody')[0];
        table.innerHTML = "";
        var start = (currentPage - 1) * rowsPerPage;
        var end = start + rowsPerPage;
        var paginatedSpecials = specials.slice(start, end);

        for (var i = 0; i < paginatedSpecials.length; i++) {
            addRowToTable(paginatedSpecials[i].id, paginatedSpecials[i].resort_id, paginatedSpecials[i].resort, paginatedSpecials[i].name, paginatedSpecials[i].description, paginatedSpecials[i].expiration, paginatedSpecials[i].disabled);
        }
    }

    function setupPagination() {
        var pagination = document.getElementById("pagination");
        pagination.innerHTML = "";

        var pageCount = Math.ceil(specials.length / rowsPerPage);
        if (pageCount <= 1) {
            pagination.style.display = "none";
        } else {
            pagination.style.display = "block";
            for (var i = 1; i <= pageCount; i++) {
                var button = document.createElement("button");
                button.textContent = i;
                if (i === currentPage) {
                    button.classList.add("active");
                }
                button.addEventListener("click", function() {
                    currentPage = parseInt(this.textContent);
                    displayTable();
                    setupPagination();
                });
                pagination.appendChild(button);
            }
        }
    }

    function saveTableData() {
        var data = allSpecials.map(function(special) {
            return {
                id: special.id,
                resort_id: special.resort_id,
                resort: special.resort,
                name: special.name,
                description: special.description,
                expiration: special.expiration,
                disabled: special.disabled
            };
        });

        var results = JSON.stringify(data);

        jQuery.ajax({
            type: 'POST',
            url: vmb_ajax.ajax_url,
            data: {
                action: 'save_table',
                security: vmb_ajax.nonce,
                jsonData: results
            },
            success: function(response) {
                if(response.success) {
                    isDataSaved = true;
                    Swal.fire(
                        'Saved!',
                        'Your data has been saved.',
                        'success'
                    );
                } else {
                    Swal.fire(
                        'Failed!',
                        'Data not saved!',
                        'error'
                    );
                }
            }
        });
    }

    function filterTable() {
        var filter = document.getElementById("resortFilter").value;
        currentPage = 1; // Reset to first page on filter change
        specials = allSpecials.filter(function(special) {
            return filter === "All" || special.resort === filter;
        });
        displayTable();
        setupPagination();
    }

    function formatDate(isoDateString) {
        let date = new Date(isoDateString);
        let options = { year: 'numeric', month: 'long', day: 'numeric' };
        return date.toLocaleDateString('en-US', options);
    }

    document.getElementById('saveTableDataBtn').addEventListener('click', saveTableData);
    document.getElementById('resortFilter').addEventListener('change', filterTable);

    document.getElementById('specialsTable').addEventListener('click', function(event) {
        var row = event.target.parentNode.parentNode;
        if (event.target.classList.contains('edit-btn')) {
            toggleEdit(row);
        } else if (event.target.classList.contains('disable-btn')) {
            toggleDisable(row);
        }
    });

    window.addEventListener('beforeunload', function(event) {
        if (!isDataSaved) {
            event.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
        }
    });

    var dummyJson = JSON.stringify([
        { "id": "1", "resort_id": "101", "resort": "Resort A", "name": "Special One", "description": "Description for Special One", "expiration": "June 14, 2024", "disabled": false },
        { "id": "2", "resort_id": "102", "resort": "Resort B", "name": "Special Two", "description": "Description for Special Two", "expiration": "June 14, 2024", "disabled": false },
        { "id": "3", "resort_id": "103", "resort": "Resort C", "name": "Special Three", "description": "Description for Special Three", "expiration": "June 14, 2024", "disabled": false },
        // Add more rows as needed for testing pagination
    ]);

    loadJsonData(vmb_ajax.cached_specials);

    var resortFilter = document.getElementById('resortFilter');
    for (var i = 0; i < categories.length; i++) {
        var option = document.createElement('option');
        option.value = categories[i];
        option.text = categories[i];
        resortFilter.add(option);
    }
});
