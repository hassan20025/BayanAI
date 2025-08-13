// Add Admin Modal logic
document.addEventListener('DOMContentLoaded', function() {
  const addAdminBtn = document.getElementById('add-admin-btn');
  const addAdminModal = document.getElementById('add-admin-modal');
  const closeModalBtn = document.getElementById('close-modal-btn');
  const addAdminForm = document.getElementById('add-admin-form');

  if (addAdminBtn) {
    addAdminBtn.onclick = () => addAdminModal.classList.add('active');
  }

  if (closeModalBtn) {
    closeModalBtn.onclick = () => addAdminModal.classList.remove('active');
  }

  // Close modal when clicking outside
  if (addAdminModal) {
    addAdminModal.addEventListener('click', function(e) {
      if (e.target === addAdminModal) {
        addAdminModal.classList.remove('active');
      }
    });
  }

  // Close modal with ESC key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && addAdminModal && addAdminModal.classList.contains('active')) {
      addAdminModal.classList.remove('active');
    }
  });

  if (addAdminForm) {
    addAdminForm.onsubmit = async function(e) {
      e.preventDefault();
      
      const name = document.getElementById('admin-name').value;
      const email = document.getElementById('admin-email').value;
      
      const formData = new FormData();
      formData.append('name', name);
      formData.append('email', email);
      
      console.log('Sending admin data:', { name, email });
      
      try {
                                             const res = await fetch('/bayanAI/api/addAdmin.php', {
          method: 'POST',
          body: formData
        });
        
        if (!res.ok) {
          throw new Error(`HTTP error! status: ${res.status}`);
        }
        
        const data = await res.json();
        console.log('Server response:', data);
        
        if (data.error) {
          alert("Server error: " + data.error);
          return;
        }
        
        // Refresh the table to show the new admin
        if (typeof refreshAdminsTable === 'function') {
          await refreshAdminsTable();
        } else {
          // Fallback: manually append the row if refreshAdminsTable is not available
          const adminsTableBody = document.getElementById('admins-table-body');
          if (adminsTableBody) {
            const row = document.createElement('tr');
            row.innerHTML = `
              <td>${data.id || adminsTableBody.children.length + 1}</td>
              <td>${data.username || name}</td>
              <td>${data.email || email}</td>
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
            adminsTableBody.appendChild(row);
          }
        }
        
        addAdminModal.classList.remove('active');
        addAdminForm.reset();
        
      } catch (err) {
        console.error("Error adding admin:", err);
        alert("Error adding admin: " + err.message);
      }
    };
  }
}); 