// Add Company Modal logic
document.addEventListener('DOMContentLoaded', function() {
  const addCompanyBtn = document.getElementById('add-company-btn');
  const addCompanyModal = document.getElementById('add-company-modal');
  const closeModalBtn = document.getElementById('close-modal-btn');
  const addCompanyForm = document.getElementById('add-company-form');

  if (addCompanyBtn) {
    addCompanyBtn.onclick = () => addCompanyModal.classList.add('active');
  }

  if (closeModalBtn) {
    closeModalBtn.onclick = () => addCompanyModal.classList.remove('active');
  }

  // Close modal when clicking outside
  if (addCompanyModal) {
    addCompanyModal.addEventListener('click', function(e) {
      if (e.target === addCompanyModal) {
        addCompanyModal.classList.remove('active');
      }
    });
  }

  // Close modal with ESC key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && addCompanyModal && addCompanyModal.classList.contains('active')) {
      addCompanyModal.classList.remove('active');
    }
  });

  if (addCompanyForm) {
    addCompanyForm.onsubmit = async function(e) {
      e.preventDefault();
      
      const name = document.getElementById('company-name').value;
      const email = document.getElementById('company-email').value;
      const industry = document.getElementById('company-industry').value;
      const plan = document.getElementById('company-plan').value;
      const nextDue = document.getElementById('company-next-due').value;
      
      const formData = new FormData();
      formData.append('name', name);
      formData.append('email', email);
      formData.append('industry', industry);
      formData.append('plan', plan);
      formData.append('next_due', nextDue);
      
      console.log('Sending company data:', { name, email, industry, plan, nextDue });
      
      try {
        const res = await fetch('../api/addCompany.php', {
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
        
        // Add the new company to the table immediately
        const companiesTableBody = document.getElementById('companies-table-body');
        if (companiesTableBody && data.success) {
          const newRow = document.createElement('tr');
          newRow.innerHTML = `
            <td>${data.id}</td>
            <td>${data.name}</td>
            <td>${data.email}</td>
            <td>${data.industry}</td>
            <td>${data.plan}</td>
            <td>${data.next_due}</td>
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
          companiesTableBody.insertBefore(newRow, companiesTableBody.firstChild);
          console.log('New company added to UI immediately');
        }
        
        addCompanyModal.classList.remove('active');
        addCompanyForm.reset();
        
      } catch (err) {
        console.error("Error adding company:", err);
        alert("Error adding company: " + err.message);
      }
    };
  }
}); 