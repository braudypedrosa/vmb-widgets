<div class="vmb-widgets-container">
    <h2>Specials Management</h2>
    
    <label for="resortFilter">Filter by Resort:</label>
    <select id="resortFilter">
        <option value="All">All</option>
    </select>

    <table id="specialsTable">
        <thead>
            <tr>
                <th style="width: 5%;">Special ID</th>
                <th style="width: 15%;">Resort</th>
                <th style="width: 15%;">Special Name</th>
                <th style="width: 33%;">Special Description</th>
                <th style="width: 12%;">Expiration</th>
                <th style="width: 15%;">Action</th>
            </tr>
        </thead>
        <tbody>
            <!-- Rows will be added here -->
        </tbody>
    </table>

    <div class="pagination" id="pagination"></div>

    <button class="vmb-button" id="saveTableDataBtn">Save Data</button>
</div>