// Add User Form Handler
const addUserForm = document.getElementById('add-user-form');
const usersTableBody = document.getElementById('users-table-body');
const addUserModal = document.getElementById('add-user-modal');
const addUserBtn = document.getElementById('add-user-btn');
const closeModalBtn = document.getElementById('close-modal-btn');

// Modal functionality
if (addUserBtn) {
  addUserBtn.onclick = () => addUserModal.classList.add('active');
}

if (closeModalBtn) {
  closeModalBtn.onclick = () => addUserModal.classList.remove('active');
}

// Close modal when clicking outside
if (addUserModal) {
  addUserModal.addEventListener('click', function(e) {
    if (e.target === addUserModal) {
      addUserModal.classList.remove('active');
    }
  });
}

// Add User Form Handler
if (addUserForm) {
  addUserForm.onsubmit = async function(e) {
    e.preventDefault();
    const name = document.getElementById('user-name').value;
    const email = document.getElementById('user-email').value;
    const department = document.getElementById('user-department').value;
    const role = document.getElementById('user-role').value;

    const formData = new FormData();
    formData.append('name', name);
    formData.append('email', email);
    formData.append('department', department);
    formData.append('role', role);

    // Debug: Log the form data
    console.log('Sending form data:', {
      name: name,
      email: email,
      department: department,
      role: role
    });

    try {
      const res = await fetch('../api/addUser.php', {
        method: 'POST',
        body: formData
      });
      
      if (!res.ok) {
        throw new Error(`HTTP error! status: ${res.status}`);
      }
      
      const data = await res.json();
      
      // Debug: Log the response
      console.log('Server response:', data);

      if (data.error) {
        alert("Server error: " + data.error);
        return;
      }

      // Reload the entire table to ensure consistency
      if (typeof loadUsersFromDB === 'function') {
        await loadUsersFromDB();
      } else {
        // Fallback: add row directly if loadUsersFromDB is not available
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${data.id}</td>
          <td>${data.name}</td>
          <td>${data.email}</td>
          <td>${data.department}</td>
          <td>${data.role}</td>
          <td>
            <button class="icon-button flag-action" title="Flag">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 22V4a2 2 0 0 1 2-2h11.5a1.5 1.5 0 0 1 1.4 2.1L17 7l1.9 4.9A1.5 1.5 0 0 1 17.5 15H6a2 2 0 0 1-2-2z"/>
              </svg>
            </button>
            <button class="icon-button delete-action" title="Delete">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/>
                <line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/>
              </svg>
            </button>
          </td>
        `;
        usersTableBody.appendChild(row);
      }

      addUserModal.classList.remove('active');
      addUserForm.reset();
    } catch (err) {
      console.error("Error adding user:", err);
      alert("Error adding user: " + err.message);
    }
  };
} 